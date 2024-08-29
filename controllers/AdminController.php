<?php

/**
 * Contrôleur de la partie admin.
 */

class AdminController
{

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin(): void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    public function monitoring(): void
    {
        $this->checkIfUserIsConnected();
        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        $filter = $_GET["filter"] ?? "";

        if ($filter == "view-up") {
            usort($articles, function ($articleA, $articleB) {
                return $articleA->getNbViews() - $articleB->getNbViews();
            });
        }
        if ($filter == "view-down") {
            usort($articles, function ($articleA, $articleB) {
                return $articleB->getNbViews() - $articleA->getNbViews();
            });
        }
        if ($filter == "comment-up") {
            usort($articles, function ($articleA, $articleB) {
                return $articleA->getNbComment() - $articleB->getNbComment();
            });
        }
        if ($filter == "comment-down") {
            usort($articles, function ($articleA, $articleB) {
                return $articleB->getNbComment() - $articleA->getNbComment();
            });
        }
        if ($filter == "date-up") {
            usort($articles, function ($articleA, $articleB) {
                return ($articleA->getDateCreation() < $articleB->getDateCreation()) ? -1 : 1;
            });
        }
        if ($filter == "date-down") {
            usort($articles, function ($articleA, $articleB) {
                return ($articleB->getDateCreation() < $articleA->getDateCreation()) ? -1 : 1;
            });
        }
        if ($filter == "title-up") {
            usort($articles, function ($articleA, $articleB) {
                return strcmp($articleA->getTitle(), $articleB->getTitle());
            });
        }
        if ($filter == "title-down") {
            usort($articles, function ($articleA, $articleB) {
                return  strcmp($articleB->getTitle(), $articleA->getTitle());
            });
        }
        // exit;
        // On affiche la page d'administration.
        $view = new View("Monitoring");
        $view->render("monitoring", [
            'articles' => $articles
        ]);
    }
    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected(): void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm(): void
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser(): void
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser(): void
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**Suppression d'un commentaire     */
    public function deleteSingleComment(): void
    {
        $this->checkIfUserIsConnected();


        $id = Utils::request("id", -1);
        // On supprime un commentaire.
        $commentManager = new CommentManager;
        $comment = $commentManager->getCommentById($id);
        $idArticle = $commentManager->getArticletByIdComent($id);
        $commentManager->deleteComment($comment);
        // On redirige vers la page d'article.
        Utils::redirect("showArticle", ['id' => $idArticle["id_article"]]);
    }
}
