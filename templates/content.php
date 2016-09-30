<?php $this->layout('theme::layout/05_page') ?>
<article class="Page">

    <div class="Page__header">
        <h1><?= $page['breadcrumbs'] ? $this->get_breadcrumb_title($page, $base_page) : $page['title'] ?></h1>
        <?php if ($params['html']['date_modified']) { ?>
        <span style="float: left; font-size: 10px; color: gray;">
            <?= date("l, F j, Y g:i A", $page['modified_time']); ?>
        </span>
        <?php } ?>
        <?php
        $edit_on = $params->getHTML()->getEditOn();
        if ($edit_on) { ?>
        <span style="float: right; font-size: 10px; color: gray;">
            <a href="<?= $edit_on['basepath'] ?>/<?= $page['relative_path'] ?>" target="_blank">Edit on <?= $edit_on['name'] ?></a>
        </span>
        <?php } ?>
    </div>


    <div class="s-content">
        <?= $page['content']; ?>
    </div>

    <?php if (!empty($page['prev']) || !empty($page['next'])) {
    ?>
    <nav>
        <ul class="Pager">
            <?php if (!empty($page['prev'])) {
        ?><li class=Pager--prev><a href="<?= $base_url . $page['prev']->getUrl() ?>">Previous</a></li><?php

    } ?>
            <?php if (!empty($page['next'])) {
        ?><li class=Pager--next><a href="<?= $base_url . $page['next']->getUrl() ?>">Next</a></li><?php

    } ?>
        </ul>
    </nav>
    <?php

} ?>
</article>

