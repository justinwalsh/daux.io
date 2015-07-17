<?php $this->layout('theme::layout/05_page') ?>
<article>
    <?php if ($params['date_modified']) { ?>
        <div class="page-header sub-header clearfix">
            <h1><?php
                if ($page['breadcrumbs']) echo $this->get_breadcrumb_title($page, $base_page);
                else echo $page['title'];
                ?>
                <?php if ($params['file_editor']) echo '<a href="javascript:;" id="editThis" class="btn">Edit this page</a>'; ?>
            </h1>
                    <span style="float: left; font-size: 10px; color: gray;">
                        <?php echo date("l, F j, Y", $page['modified_time']); ?>
                    </span>
                    <span style="float: right; font-size: 10px; color: gray;">
                        <?php echo date("g:i A", $page['modified_time']); ?>
                    </span>
        </div>
    <?php } else { ?>
        <div class="page-header">
            <h1><?php
                if ($page['breadcrumbs']) echo $this->get_breadcrumb_title($page, $base_page);
                else echo $page['title'];
                ?>
                <?php if ($params['file_editor']) echo '<a href="javascript:;" id="editThis" class="btn">Edit this page</a>'; ?>
            </h1>
        </div>
    <?php } ?>

    <?php echo $page['content']; ?>
    <?php if ($params['file_editor']) { ?>
        <div class="editor<?php if (!$params['date_modified']) echo ' paddingTop'; ?>">
            <h3>You are editing <?php echo $page['path']; ?>&nbsp;<a href="javascript:;" class="closeEditor btn btn-warning">Close</a></h3>
            <div class="navbar navbar-inverse navbar-default navbar-fixed-bottom" role="navigation">
                <div class="navbar-inner">
                    <a href="javascript:;" class="save_editor btn btn-primary navbar-btn pull-right">Save file</a>
                </div>
            </div>
            <textarea id="markdown_editor"><?php echo $page['markdown']; ?></textarea>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
</article>

