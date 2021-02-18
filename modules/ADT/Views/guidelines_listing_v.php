<div id="wrapperd">

    <div id="guidelines_content" class="full-content">
        <?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v") ?>

        <?php echo $guidelines_list; ?>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $(".active").removeClass();
        $("#guidelines").addClass("active");
    });
</script>