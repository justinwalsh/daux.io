<?php $this->layout('theme::layout/05_page') ?>

<article>
    <div class="page-header">
        <h1><?php echo $page['title']; ?></h1>
    </div>

    <?php echo $page['content']; ?>
</article>
