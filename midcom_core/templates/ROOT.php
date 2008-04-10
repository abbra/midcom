<?php
/**
 * @package midcom_core
 *
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title tal:content="page/title">Midgard CMS</title>
        <span tal:replace="php: MIDCOM.head.print_elements()" />
        <link rel="stylesheet" type="text/css" href="/midcom-static/midcom_core/midgard/screen.css" media="screen,projection,tv" />
        <link rel="stylesheet" type="text/css" href="/midcom-static/midcom_core/midgard/content.css" media="all" />
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="shortcut icon" href="midgard.ico" type="image/vnd.microsoft.icon" />
    </head>
    <body>
        <div id="container">
            <div id="branding">
                <div class="grouplogo">
                    <a href="/"><img src="/midcom-static/midcom_core/midgard/midgard.gif" alt="Midgard" width="135" height="138" /></a>
                </div>
            </div>
            <div id="content">
                <!-- beginning of content-text -->
                <div id="content-text">
                    <(content)>
                </div>
            </div>
        </div>
        <div id="siteinfo">
             <a href="http://www.midgard-project.org/">Midgard CMS</a> power since 1999. <a href="http://www.gnu.org/licenses/lgpl.html">Free software</a>.
        </div>
        <span tal:condition="show_toolbar" tal:replace="php: MIDCOM.toolbar.render()" />
        <span tal:condition="uimessages" tal:replace="structure uimessages" />
    </body>
</html>