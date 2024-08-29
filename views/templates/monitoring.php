<?php

/**
 * Affichage de Liste des articles. 
 */
?>
<div class="adminArticle">
    <div class="articleLine">
        <div class="title">Titre de l'article
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=title-up"> <i class="fas fa-angle-up"></i></a>
            </div>
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=title-down"> <i class="fas fa-angle-down"></i></a>
            </div>
        </div>

        <div class="title">Nombre de vues
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=view-up"> <i class="fas fa-angle-up"></i></a>
            </div>
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=view-down"> <i class="fas fa-angle-down"></i></a>
            </div>
        </div>
        <div class="title">Nombre de commentaires
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=comment-up"> <i class="fas fa-angle-up"></i></a>
            </div>
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=comment-down"> <i class="fas fa-angle-down"></i></a>
            </div>
        </div>
        <div class="title">Date de publication
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=date-up"> <i class="fas fa-angle-up"></i></a>
            </div>
            <div>
                <a class="submit" href="index.php?action=monitoring&filter=date-down"> <i class="fas fa-angle-down"></i></a>
            </div>
        </div>


    </div>
    <?php foreach ($articles as $article) { ?>
        <div class="articleLine">
            <div class="title"><?= $article->getTitle() ?></div>
            <div class="title"><?= $article->getNbViews() ?></div>
            <div class="title"><?= $article->getNbComment() ?></div>
            <div class="title"><?=
                                Utils::convertDateToFrenchFormat($article->getDateCreation()) ?></div>
        </div>
    <?php } ?>
</div>