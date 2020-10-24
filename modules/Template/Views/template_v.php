<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <!--Load Header-->
        <?php echo view('\Modules\Template\Views\header_v'); ?>
    </head>
    <body>
        <!--Load Menu-->
        <?php echo view('\Modules\Template\Views\external_header_v'); ?>
        <!--Main Content-->
        <div class="container">
            <!--Load Content-->
            <?php echo view($content_view); ?>
        </div>
        <!--Load footer-->
        <?php echo view('\Modules\Template\Views\footer_v'); ?>
    </body>
</html>