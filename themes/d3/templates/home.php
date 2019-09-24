<?php $this->layout('theme::layout/00_layout') ?>

<?php $this->start('classes') ?>homepage<?php $this->stop() ?>

<div class="Navbar NoPrint">
    <div class="Container">
        <?php $this->insert('theme::partials/navbar_content', ['params' => $params]); ?>
    </div>
</div>

<div class="Homepage">
    <div class="HomepageTitle Container">
        <div class="title">
            <?= ($params['title'])? '<h2>' . $params['title'] . '</h2>' : '' ?>
            <?= ($params['titledesc'])? '<div>' . $params['titledesc'] . '</div>' : '' ?>
        </div>

        <?= ($params['image'])? '<img class="homepage-image img-responsive" src="' . $params['image'] . '" alt="' . $params['title'] . '">' : '' ?>

        <div class="details">
            <?= ($params['author'])? '<div>' . $this->translate("author") . ': ' . $params['author'] . '</div>' : '' ?>
            <?= ($params['moduledate'])? '<div>' . $this->translate("moduledate") . ': ' . $params['moduledate'] . '</div>' : '' ?>

            <?php

            if (isset($params['versionselector']) && $params['versionselector'] && 
                isset($params['versiondirectoryindex']) && $params['versiondirectoryindex']) {
                echo "<div>";
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
            } else {
                echo ($params['moduleversion'])? '<div>' . $this->translate("version") . ': ' . $params['moduleversion'] . '</div>' : '';
            }
            ?>

            <?= ($params['editors'])? '<div>' . $this->translate("editors") . ': ' . $params['editors'] . '</div>' : '' ?>
        </div>
    </div>

    <div class="HomepageButtons">
        <div class="Container">
            <?php
            if ($params['html']['repo']) {
                echo '<a href="https://github.com/' . $params['html']['repo'] . '" class="Button Button--secondary Button--hero">' . $this->translate("View_on_github") . '</a>';
            }
            $view_doc = $this->translate("View_documentation");
            foreach ($page['entry_page'] as $key => $node) {
                echo '<a href="' . $node . '" class="Button Button--primary Button--hero">' . str_replace("__VIEW_DOCUMENTATION__", $view_doc, $key) . '</a>';
            }
            if(isset($params['html']['buttons']) && is_array($params['html']['buttons'])) {
                foreach ($params['html']['buttons'] as $name => $link ) {
                    echo '<a href="' . $link . '" class="Button Button--secondary Button--hero">' . $name . '</a>';
                }
            }
            ?>
        </div>
    </div>
</div>

<div class="HomepageContent">
    <div class="Container">
        <div class="Container--inner">
            <div class="doc_content s-content">
                <?= $page['content']; ?>
            </div>
        </div>
    </div>
</div>

<div class="HomepageFooter">
    <div class="Container">
        <div class="Container--inner">
            <ul class="HomepageFooter__links">
                <li><a href="https://www.oxidmodule.com" target="_blank">Shop</a></li>
                <li><a href="https://blog.oxidmodule.com" target="_blank">Blog</a></li>
                <li><a href="https://faq.d3data.de" target="_blank">FAQ</a></li>
                <li><a href="https://docs.oxidmodule.com" target="_blank">Dokumentationen</a></li>
                <li><a href="https://support.oxidmodule.com" target="_blank">D³ Support Center</a></li>
                <li><a href="https://www.oxidmodule.com/kontakt" target="_blank">Kontakt</a></li>
                <li><a href="https://www.oxidmodule.com/impressum" target="_blank">Impressum</a></li>
                <?php if (!empty($params['html']['links'])) { ?>
                    <?php foreach ($params['html']['links'] as $name => $url) {
                        if (!in_array($name, array('Shop', 'Blog', 'FAQ', 'D³ Support-Center', 'Kontakt', 'Impressum'))) {
                            echo '<li><a href="' . $url . '" target="_blank">' . $name . '</a></li>';
                        }
                    } ?>
                <?php } ?>
            </ul>

            <?php if (!empty($params['html']['twitter'])) { ?>
                <div class="HomepageFooter__twitter">
                    <?php foreach ($params['html']['twitter'] as $handle) { ?>
                    <div class="Twitter">
                        <iframe allowtransparency="true" frameborder="0" scrolling="no" style="width:162px; height:20px;" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=<?= $handle; ?>&amp;show_count=false"></iframe>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

