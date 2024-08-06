<?php
echo '<script src=\'https://cdn.ckeditor.com/4.19.1/standard-all/ckeditor.js\'></script>';
echo '<script src="'. $baseDirCss .'js/nav.js"></script>';
?>



<script>
    document.addEventListener('DOMContentLoaded', function() {

      CKEDITOR.replace('editor', {
        height: '90vh',
        width: '100%',
        uiColor: '#d4d4d4',
      });

      var pageEffect = document.getElementById('page_effect');
      if (pageEffect) {
        pageEffect.style.display = 'block'; 
        pageEffect.style.transition = 'opacity 2s'; opacidade
        pageEffect.style.opacity = '0'; 
        setTimeout(function() {
          pageEffect.style.opacity = '1'; 
        }, 50); 
      }
    });
</script>