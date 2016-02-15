<a class="brand navbar-brand pull-left" href="<?= $params['base_page'] . $params['index']->getUri(); ?>"><?= $params['title']; ?></a>

<?php if ($params['text_search']) { ?>
    <p class="navbar-text pull-right">
        <div style="margin-top: 11px">
            <input type="search" id="tipue_search_input" placeholder="Search..." autocomplete="on" results=25 autosave=text_search>
        </div>
    </p>
<?php } ?>
