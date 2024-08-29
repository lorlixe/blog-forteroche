<?php

class ArticleController
{
    /**
     * Affiche la page d'accueil.
     * @return void
     */
    public function showHome(): void
    {
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        $view = new View("Accueil");
        $view->render("home", ['articles' => $articles]);
    }

    /**
     * trier les article.
     * @return void
     */
    public function showByViews(): void
    {

        $articleManager = new ArticleManager();
        $articles = $articleManager->orderByView();


        $view = new View("showByViews");
        $view->render("showByViews", ['articles' => $articles]);
    }




    /**
     * Affiche le détail d'un article.
     * @return void
     */
    public function showArticle(): void
    {
        // Récupération de l'id de l'article demandé.
        $id = Utils::request("id", -1);

        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);
        // var_dump($article);
        // exit;

        if (!$article) {
            throw new Exception("L'article demandé n'existe pas.");
        }
        $articleManager->updateViewsInDatabase($article);

        $commentManager = new CommentManager();
        $comments = $commentManager->getAllCommentsByArticleId($id);

        $view = new View($article->getTitle());
        $view->render("detailArticle", ['article' => $article, 'comments' => $comments]);
    }

    /**
     * Affiche le formulaire d'ajout d'un article.
     * @return void
     */
    public function addArticle(): void
    {
        $view = new View("Ajouter un article");
        $view->render("addArticle");
    }

    /**
     * Affiche la page "à propos".
     * @return void
     */
    public function showApropos()
    {
        $view = new View("A propos");
        $view->render("apropos");
    }
}
