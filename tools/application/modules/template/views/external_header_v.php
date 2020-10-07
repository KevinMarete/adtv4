<nav class="navbar navbar-default navbar-fixed-top" style="background: #2B597E;">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!-- <a class="navbar-brand" href="#">ADT Tools</a> -->
      <a class="navbar-brand" href="<?= str_replace("tools/", "", base_url())?>">ADT v<?= $this->config->item('adt_version'); ?> Login </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">

      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?= base_url() ?>">Recover <span class="sr-only">(current)</span></a></li>
        <li><a href="<?= base_url() ?>backup">Backup<span class="sr-only">(current)</span></a></li>
       <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Migration <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?= base_url();?>migrate/access">Access to webADT Migration</a></li>
            <li><a href="<?= base_url();?>migrate/editt">EDITT to webADT Migration</a></li>
          </ul>
        </li>
        <li><a href="<?= base_url() ?>github">System Update</a></li>
        <li><a href="<?= base_url() ?>adminer/index.php" target="_blank">DB Manager</a></li>
        <li><a href="<?= base_url() ?>help">Help</a></li>
        <li><a href="<?= base_url() ?>setup">Initial Setup</a></li>
        <li><a href="<?= base_url() ?>api/settings">API Settings</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>
<script type="text/javascript">
  $(function(){
    $('.nav li a, .navbar-brand').css('color','#fff');
    $('.nav li:nth-child(<?= $active_menu;?>)').addClass('active');
    $('.nav .active a').css('color','grey');
    $('.dropdown li a').css('color','black')

  });
</script>