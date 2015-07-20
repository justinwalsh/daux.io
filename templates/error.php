<?php $this->layout('theme::layout/05_page') ?>

<article>
    <div class="page-header">
        <h1><?= $page['title']; ?></h1>
    </div>

    <?= $page['content']; ?>
</article>
