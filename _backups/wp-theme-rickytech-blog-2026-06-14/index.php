<?php
// Fallback — WordPress sempre usa front-page.php ou archive.php antes deste.
get_header();
?>
<div class="container-wide" style="padding:80px 0;text-align:center;color:var(--fg-tertiary)">
  <p>Nenhum conteúdo encontrado.</p>
  <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Ir para a home</a>
</div>
<?php get_footer(); ?>
