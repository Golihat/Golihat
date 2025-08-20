<?php
/**
 * Plugin Name: Kryddbox Smakprofilör (Clean)
 * Description: Minimal, robust smakprofilör med intro-knapp, 20 frågor, 10 profiler. Inga debug/extra hookar.
 * Version: 2.8.0
 * Author: Kryddbox
 * Text Domain: kbsp
 */

if ( ! defined('ABSPATH') ) exit;

define('KBSP_VERSION', '2.4.2');
define('KBSP_DIR', plugin_dir_path(__FILE__));
define('KBSP_URL', plugin_dir_url(__FILE__));

// Shortcode output
function kbsp_render_shortcode($atts = array(), $content = ''){
  ob_start(); ?>
  <div id="kbsp-root" class="kbsp-app" data-version="<?php echo esc_attr(KBSP_VERSION); ?>"></div>
  <?php return ob_get_clean();
}
add_shortcode('kryddbox_smakprofilor', 'kbsp_render_shortcode');

// Always enqueue on frontend
function kbsp_enqueue_assets(){
  if (is_admin()) return;
  wp_enqueue_style('kbsp-app', KBSP_URL.'assets/css/app.css', array(), KBSP_VERSION);
  wp_enqueue_script('kbsp-app', KBSP_URL.'assets/js/app.js', array(), KBSP_VERSION, true);

  // Embedded seed
  $seed = array();
  // Load from option if present (string JSON or array)
  $opt = get_option('kbsp_seed', '');
  if (is_string($opt) && $opt !== '') {
    $tmp = json_decode($opt, true);
    if (is_array($tmp)) $seed = $tmp;
  } elseif (is_array($opt)) {
    $seed = $opt;
  }
  $p = KBSP_DIR.'assets/seed/seed.json';
  if (empty($seed) && file_exists($p)) {
    $raw = file_get_contents($p);
    $json = json_decode($raw, true);
    if (is_array($json)) $seed = $json;
  }

  // Ensure stable dimension order incl. 'salt'
  if (empty($seed['settings']['dimensions']) || !is_array($seed['settings']['dimensions'])) {
    $seed['settings']['dimensions'] = array_fill_keys(
      array('comfort','jordig','hetta','syra','street','umami','fine','fräsch','sötma','salt'), array()
    );
  } else {
    $keys = array('comfort','jordig','hetta','syra','street','umami','fine','fräsch','sötma','salt');
    $dim = array();
    foreach ($keys as $k){ $dim[$k] = isset($seed['settings']['dimensions'][$k]) ? $seed['settings']['dimensions'][$k] : array(); }
    $seed['settings']['dimensions'] = $dim;
  }

  wp_localize_script('kbsp-app','KBSP', array(
    'rest'     => esc_url_raw( rest_url('kbsp/v1/') ),
    'rest_alt' => esc_url_raw( home_url('/?rest_route=/kbsp/v1/') ),
    'assets'   => KBSP_URL.'assets/',
    'ui' => get_option('kbsp_ui', array('hero_title'=>'Upptäck din unika smakprofil','hero_subtitle'=>'Ta fram din egna smakprofil med hjälp av vår smakprofilör.')),
    'embedded' => array(
      'questions' => isset($seed['questions']) ? $seed['questions'] : array(),
      'profiles'  => isset($seed['profiles'])  ? $seed['profiles']  : array(),
      'settings'  => isset($seed['settings'])  ? $seed['settings']  : array(),
    ),
  ));
}
add_action('wp_enqueue_scripts','kbsp_enqueue_assets',5);

// Ensure boot after DOM ready (single point)
function kbsp_footer_boot(){
  if (is_admin()) return; ?>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.KBSP_START) { try { window.KBSP_START(); } catch(e){ console.error(e); } }
    });
  </script>
<?php }
add_action('wp_footer','kbsp_footer_boot', 99);

// ---------------------- ADMIN ----------------------
if (is_admin()){
  add_action('admin_menu', function(){
    add_menu_page('Smakprofilör','Smakprofilör','manage_options','kbsp_admin','kbsp_admin_page','dashicons-filter',58);
  });

  function kbsp_admin_page(){
    if (!current_user_can('manage_options')) return;

    $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'fragor';
    $seed = array();
    $opt = get_option('kbsp_seed','');
    if (is_string($opt) && $opt !== ''){ $tmp = json_decode($opt, true); if (is_array($tmp)) $seed = $tmp; }
    elseif (is_array($opt)){ $seed = $opt; }
    if (empty($seed)){
      $p = KBSP_DIR.'assets/seed/seed.json';
      if (file_exists($p)) { $seed = json_decode(file_get_contents($p), true); }
    }
    $ui  = get_option('kbsp_ui', array('hero_title'=>'Upptäck din unika smakprofil','hero_subtitle'=>'Ta fram din egna smakprofil med hjälp av vår smakprofilör.'));

    // handle actions
    if (isset($_POST['kbsp_save_seed']) && check_admin_referer('kbsp_save_seed')){
      $json = wp_unslash($_POST['kbsp_seed_json'] ?? '');
      $arr = json_decode($json, true);
      if (is_array($arr)){
        update_option('kbsp_seed', json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        echo '<div class="updated"><p>Seed sparat.</p></div>';
        $seed = $arr;
      } else {
        echo '<div class="error"><p>Ogiltig JSON – inget sparat.</p></div>';
      }
    }

    if (isset($_POST['kbsp_save_ui']) && check_admin_referer('kbsp_save_ui')){
      $hero_title = sanitize_text_field($_POST['kbsp_hero_title'] ?? '');
      $hero_sub   = sanitize_text_field($_POST['kbsp_hero_sub'] ?? '');
      $ui = array('hero_title'=>$hero_title ?: 'Upptäck din unika smakprofil','hero_subtitle'=>$hero_sub ?: 'Ta fram din egna smakprofil med hjälp av vår smakprofilör.');
      update_option('kbsp_ui', $ui);
      echo '<div class="updated"><p>Texter sparade.</p></div>';
    }

    if (isset($_GET['kbsp_export']) && check_admin_referer('kbsp_export')){
      $data = get_option('kbsp_seed','');
      if (is_array($data)) { $data = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
      if (empty($data) && !empty($seed)) $data = json_encode($seed, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
      header('Content-Type: application/json; charset=utf-8');
      header('Content-Disposition: attachment; filename="kbsp-seed.json"');
      echo $data; exit;
    }

    if (isset($_POST['kbsp_import_seed']) && check_admin_referer('kbsp_import_seed') && !empty($_FILES['kbsp_seed_file']['tmp_name'])){
      $raw = file_get_contents($_FILES['kbsp_seed_file']['tmp_name']);
      $arr = json_decode($raw, true);
      if (is_array($arr)){
        update_option('kbsp_seed', json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        echo '<div class="updated"><p>Seed importerad.</p></div>';
        $seed = $arr;
      } else {
        echo '<div class="error"><p>Ogiltig JSON – inget importerades.</p></div>';
      }
    }

    ?>
    <div class="wrap">
      <h1>Smakprofilör</h1>
      <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=kbsp_admin&tab=fragor'); ?>" class="nav-tab <?php echo $tab==='fragor'?'nav-tab-active':''; ?>">Frågor</a>
        <a href="<?php echo admin_url('admin.php?page=kbsp_admin&tab=profiler'); ?>" class="nav-tab <?php echo $tab==='profiler'?'nav-tab-active':''; ?>">Profiler & horoskop</a>
        <a href="<?php echo admin_url('admin.php?page=kbsp_admin&tab=utseende'); ?>" class="nav-tab <?php echo $tab==='utseende'?'nav-tab-active':''; ?>">Utseende & texter</a>
        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=kbsp_admin&kbsp_export=1'),'kbsp_export'); ?>" class="nav-tab">Export</a>
      </h2>

      <?php if($tab==='fragor'): ?>
        <p>Redigera seed (JSON). Detta styr frågor, val, bilder och vikter.</p>
        <form method="post">
          <?php wp_nonce_field('kbsp_save_seed'); ?>
          <textarea name="kbsp_seed_json" rows="24" style="width:100%; font-family:Menlo,Consolas,monospace;"><?php echo esc_textarea(json_encode($seed, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); ?></textarea>
          <p><button class="button button-primary" name="kbsp_save_seed" value="1">Spara ändringar</button></p>
        </form>
        <hr/>
        <h3>Importera</h3>
        <form method="post" enctype="multipart/form-data">
          <?php wp_nonce_field('kbsp_import_seed'); ?>
          <input type="file" name="kbsp_seed_file" accept="application/json"/>
          <button class="button" name="kbsp_import_seed" value="1">Importera</button>
        </form>
      <?php elseif($tab==='profiler'): ?>
        <p>Snabbredigera profiler (namn, nyckelord och horoskop). För avancerad redigering, använd JSON‑fliken.</p>
        <form method="post">
          <?php wp_nonce_field('kbsp_save_seed'); ?>
          <?php if(!empty($seed['profiles'])): foreach($seed['profiles'] as $i=>$p): ?>
            <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin:10px 0; background:#fff;">
              <p><strong>Profil <?php echo $i+1; ?></strong></p>
              <label>Slug<br/><input type="text" name="profiles[<?php echo $i; ?>][slug]" value="<?php echo esc_attr($p['slug'] ?? ''); ?>" class="regular-text"/></label><br/>
              <label>Namn<br/><input type="text" name="profiles[<?php echo $i; ?>][name]" value="<?php echo esc_attr($p['name'] ?? ''); ?>" class="regular-text"/></label><br/>
              <label>Nyckelord (komma‑separerade)<br/><input type="text" name="profiles[<?php echo $i; ?>][keywords]" value="<?php echo esc_attr(isset($p['keywords'])?implode(', ', (array)$p['keywords']):''); ?>" class="regular-text"/></label><br/>
              <label>Horoskop<br/><textarea name="profiles[<?php echo $i; ?>][horoscope_default]" rows="6" style="width:100%;"><?php echo esc_textarea($p['horoscope_default'] ?? ''); ?></textarea></label>
            </div>
          <?php endforeach; endif; ?>
          <p><em>Obs: Denna snabbredigering ersätter motsvarande fält i seed. För dimensionsmatchning redigerar du JSON.</em></p>
          <p><button class="button button-primary" name="kbsp_save_seed" value="1">Spara profiler</button></p>
          <input type="hidden" name="kbsp_seed_json" value="<?php echo esc_attr(json_encode($seed, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); ?>"/>
        </form>
        <script>
        // När profilformuläret skickas, byggs seed från hidden med uppdaterade fält
        (function(){
          const form = document.currentScript.closest('form');
          form.addEventListener('submit', function(){
            try{
              const seed = JSON.parse(form.querySelector('input[name="kbsp_seed_json"]').value);
              const blocks = form.querySelectorAll('div[style*="border:1px"]');
              blocks.forEach((b,i)=>{
                const slug  = b.querySelector('input[name^="profiles['+i+']"][name$="[slug]"]').value;
                const name  = b.querySelector('input[name^="profiles['+i+']"][name$="[name]"]').value;
                const keys  = b.querySelector('input[name^="profiles['+i+']"][name$="[keywords]"]').value.split(',').map(s=>s.trim()).filter(Boolean);
                const text  = b.querySelector('textarea[name^="profiles['+i+']"][name$="[horoscope_default]"]').value;
                seed.profiles[i] = Object.assign({}, seed.profiles[i]||{}, {slug, name, keywords:keys, horoscope_default:text});
              });
              form.querySelector('input[name="kbsp_seed_json"]').value = JSON.stringify(seed);
            }catch(e){ console.error(e); }
          });
        })();
        </script>
      <?php elseif($tab==='utseende'): ?>
        <form method="post">
          <?php wp_nonce_field('kbsp_save_ui'); ?>
          <table class="form-table">
            <tr><th scope="row"><label for="kbsp_hero_title">Hero‑titel</label></th>
                <td><input type="text" id="kbsp_hero_title" name="kbsp_hero_title" class="regular-text" value="<?php echo esc_attr($ui['hero_title'] ?? ''); ?>"/></td></tr>
            <tr><th scope="row"><label for="kbsp_hero_sub">Hero‑underrubrik</label></th>
                <td><input type="text" id="kbsp_hero_sub" name="kbsp_hero_sub" class="regular-text" value="<?php echo esc_attr($ui['hero_subtitle'] ?? ''); ?>"/></td></tr>
          </table>
          <p><button class="button button-primary" name="kbsp_save_ui" value="1">Spara</button></p>
        </form>
      <?php endif; ?>
    </div>
    <?php
  }
}

// KBSP sharecard (server OG) -- minimal, safe
add_filter('query_vars', function($vars){ $vars[]='kbsp_share'; $vars[]='kbsp_profile'; return $vars; });
add_action('template_redirect', function(){
  if (intval(get_query_var('kbsp_share')) !== 1) return;
  $slug = sanitize_text_field( get_query_var('kbsp_profile') );
  $target = isset($_GET['target']) ? base64_decode(sanitize_text_field($_GET['target'])) : '';
  $seed = kbsp_get_seed();
  $profiles = isset($seed['profiles']) ? $seed['profiles'] : array();
  $profile = null;
  foreach ($profiles as $p){ if (!empty($p['slug']) && $p['slug']===$slug){ $profile=$p; break; } }
  if (!$profile && !empty($profiles)) $profile = $profiles[0];
  $name = $profile && !empty($profile['name']) ? $profile['name'] : 'min smakprofil';

  $title = 'Jag fick "'.$name.'" som smakprofil på Kryddbox – testa din smakprofil!';
  $desc  = 'Gör vårt snabba smaktest och få en personlig text och träffsäkra kryddrekommendationer.';
  $img   = plugin_dir_url(__FILE__) . 'assets/share/kbsp-share-default.png';
  $url   = home_url( add_query_arg(array('kbsp_share'=>1,'kbsp_profile'=>$slug), '/') );

  header('Content-Type: text/html; charset=utf-8');
  echo '<!doctype html><html lang="sv"><head>';
  echo '<meta charset="utf-8"/>';
  echo '<meta property="og:type" content="article" />';
  echo '<meta property="og:title" content="'.esc_attr($title).'" />';
  echo '<meta property="og:description" content="'.esc_attr($desc).'" />';
  echo '<meta property="og:image" content="'.esc_url($img).'" />';
  echo '<meta property="og:url" content="'.esc_url($url).'" />';
  echo '<meta name="twitter:card" content="summary_large_image" />';
  echo '<meta name="twitter:title" content="'.esc_attr($title).'" />';
  echo '<meta name="twitter:description" content="'.esc_attr($desc).'" />';
  echo '<meta name="twitter:image" content="'.esc_url($img).'" />';
  echo '<meta name="robots" content="noindex,follow" />';
  echo '</head><body><p>Delningskort för '.esc_html($name).'.</p></body></html>';
  exit;
});
