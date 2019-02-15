<?php $this->layout('theme::layout/00_layout') ?>
<div class="Columns content">
    <aside class="Columns__left Collapsible">
        <button type="button" class="Button Collapsible__trigger">
            <span class="Collapsible__trigger__bar"></span>
            <span class="Collapsible__trigger__bar"></span>
            <span class="Collapsible__trigger__bar"></span>
        </button>

        <?php $this->insert('theme::partials/navbar_content', ['params' => $params]); ?>

        <div class="Collapsible__content">
            <!-- Navigation -->
            <?php
            $rendertree = $tree;
            $path = '';

            if ($page['language'] !== '') {
                $rendertree = $tree[$page['language']];
                $path = $page['language'];
            }

            echo $this->get_navigation($rendertree, $path, isset($params['request']) ? $params['request'] : '', $base_page, $params['mode']);
            ?>

            <?php

            if (isset($params['versionselector']) && $params['versionselector'] && 
                isset($params['versiondirectoryindex']) && $params['versiondirectoryindex']) {
                echo "<div class='versionselector'>";
                echo $this->translate("selectversion").': ';

                $code = '
<select onchange="window.location.href=this.options[this.selectedIndex].value" size="1">

<?php
    $versionpath = implode("/", array_slice(explode("/", $_SERVER[\'SCRIPT_NAME\']), '. $params['versiondirectoryindex'] .'));
    $modulepath = implode("/", array_slice(explode("/", $_SERVER[\'SCRIPT_NAME\']), 0, '. $params['versiondirectoryindex'] .'));
    $path = str_replace($versionpath, "", $_SERVER[\'SCRIPT_FILENAME\']);
    $paths = explode(\'/\', $versionpath);
    $currpath = $paths[0];
    
    $dirs = array_filter(glob($path . \'/*\'), \'is_dir\');
    arsort($dirs);

    foreach ($dirs as $dir) {
        $dir = str_replace($path.\'/\', \'\', $dir);
        $selected = ($dir === $currpath) ? \'selected="selected"\' : "";
        echo \'<option value="\'. $modulepath .\'/\'. $dir .\'" \'. $selected .\'>\'. $dir .\'</option>\';
    }
?>
';

                echo $code;
                echo "</select>";
                echo "</div>";
            }
            ?>



            <div class="Links">
                <hr/>
                <a href="https://www.oxidmodule.com" target="_blank">Shop</a><br />
                <a href="https://blog.oxidmodule.com" target="_blank">Blog</a><br />
                <a href="https://faq.oxidmodule.com" target="_blank">FAQ</a><br />
                <a href="https://docs.oxidmodule.com" target="_blank">Dokumentationen</a><br />
                <a href="https://support.oxidmodule.com" target="_blank">D³ Support Center</a><br />
                <a href="https://www.oxidmodule.com/kontakt" target="_blank">Kontakt</a><br />
                <a href="https://www.oxidmodule.com/impressum" target="_blank">Impressum</a><br />
                <?php if (!empty($params['html']['links'])) {
                    foreach ($params['html']['links'] as $name => $url) { 
                        if (!in_array($name, array('Shop', 'Blog', 'FAQ', 'D³ Support-Center', 'Kontakt', 'Impressum'))) { ?>
                            <a href="<?= $url ?>" target="_blank"><?= $name ?></a>
                            <br />
                        <?php } 
                    }
                } ?>
            </div>

            <?php if ($params['html']['toggle_code']) { ?>
                    <div class="CodeToggler">
                        <hr/>
                        <?php if ($params['html']['float']) { ?>
                            <span class="CodeToggler__text"><?=$this->translate("CodeBlocks_title") ?></span>
                            <div class="ButtonGroup" role="group">
                                <button class="Button Button--default Button--small CodeToggler__button CodeToggler__button--hide"><?=$this->translate("CodeBlocks_hide") ?></button>
                                <button class="Button Button--default Button--small CodeToggler__button CodeToggler__button--below"><?=$this->translate("CodeBlocks_below") ?></button>
                                <button class="Button Button--default Button--small CodeToggler__button CodeToggler__button--float"><?=$this->translate("CodeBlocks_inline") ?></button>
                            </div>
                        <?php } else { ?>
                            <label class="Checkbox"><?=$this->translate("CodeBlocks_show") ?>
                                <input type="checkbox" class="CodeToggler__button--main" checked="checked"/>
                                <div class="Checkbox__indicator"></div>
                            </label>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($params['html']['twitter'])) { ?>
                    <div class="Twitter">
                        <hr/>
                        <?php foreach ($params['html']['twitter'] as $handle) { ?>
                            <iframe allowtransparency="true" frameborder="0" scrolling="no" style="width:162px; height:20px;" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=<?= $handle; ?>&amp;show_count=false"></iframe>
                            <br />
                            <br />
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($params['html']['powered_by'])) { ?>
                    <div class="PoweredBy">
                        <hr/>
                        <?= $params['html']['powered_by'] ?>
                    </div>
                <?php } ?>
        </div>
    </aside>
    <div class="Columns__right <?= $params['html']['float'] ? 'Columns__right--float' : 'Columns__right--full'; ?>">
        <div class="Columns__right__content">
            <div class="doc_content">
                <?= $this->section('content'); ?>
            </div>
        </div>
    </div>
</div>
