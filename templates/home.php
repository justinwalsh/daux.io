<?php $this->layout('theme::layout/00_layout') ?>
<div class="navbar navbar-static-top hidden-print">
    <div class="container">
        <?php $this->insert('theme::partials/navbar_content', ['params' => $params]); ?>
    </div>
</div>
<?php if ($params['html']['repo']) { ?>
    <a href="https://github.com/<?= $params['html']['repo']; ?>" target="_blank" id="github-ribbon" class="hidden-print"><img src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
<?php } ?>

<div class="homepage-hero container-fluid">
    <div class="container">
        <div class="row">
            <div class="text-center col-sm-12">
                <?php if ($params['tagline']) {
                    echo '<h2>' . $params['tagline'] . '</h2>';
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <?php if ($params['image']) {
                    echo '<img class="homepage-image img-responsive" src="' . $params['image'] . '" alt="' . $params['title'] . '">';
                } ?>
            </div>
        </div>
    </div>
</div>

<div class="hero-buttons container-fluid">
    <div class="container text-center">
        <?php
        if ($params['html']['repo']) {
            echo '<a href="https://github.com/' . $params['html']['repo'] . '" class="btn btn-secondary btn-hero">View On GitHub</a>';
        }
        foreach ($page['entry_page'] as $key => $node) {
            echo '<a href="' . $node . '" class="btn btn-primary btn-hero">' . $key . '</a>';
        }
        ?>
        <div class="clearfix"></div>
    </div>
</div>

<div class="homepage-content container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <?php if ($params['html']['search']) { ?>
                    <div id="tipue_search_content" style="display:none"></div>
                <?php } ?>

                <div class="doc_content">
                    <?= $page['content']; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="homepage-footer container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-sm-5 col-sm-offset-1">
                <?php if (!empty($params['html']['links'])) { ?>
                    <ul class="footer-nav">
                        <?php foreach ($params['html']['links'] as $name => $url) {
                            echo '<li><a href="' . $url . '" target="_blank">' . $name . '</a></li>';
                        } ?>
                    </ul>
                <?php } ?>
            </div>
            <div class="col-sm-5">
                <div class="pull-right">
                    <?php
                    if (!empty($params['html']['twitter'])) {
                        foreach ($params['html']['twitter'] as $handle) {
                            ?>
                            <div class="twitter">
                                <iframe allowtransparency="true" frameborder="0" scrolling="no" style="width:162px; height:20px;" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=<?= $handle; ?>&amp;show_count=false"></iframe>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
