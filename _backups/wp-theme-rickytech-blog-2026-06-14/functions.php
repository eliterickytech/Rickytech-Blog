<?php
defined('ABSPATH') || exit;

/* ============================================================
   CATEGORY CONFIG
   ============================================================ */
function rt_get_cat_config(string $slug): array {
    $cats = [
        'ia'       => ['name' => 'IA & Machine Learning', 'short' => 'IA',         'ink' => '#8b6dff', 'deep' => '#1a1340', 'glyph' => '∑',   'blurb' => 'Modelos, LLMs, agentes e a engenharia por trás da inteligência artificial aplicada.'],
        'eng'      => ['name' => 'Engenharia de Software', 'short' => 'Engenharia', 'ink' => '#22d3ee', 'deep' => '#0a2b33', 'glyph' => '{}',  'blurb' => 'Arquitetura, design de sistemas, qualidade e as práticas que sustentam software de produção.'],
        'cloud'    => ['name' => 'Cloud & DevOps',         'short' => 'Cloud',      'ink' => '#34d399', 'deep' => '#06281d', 'glyph' => '☁',   'blurb' => 'Infraestrutura, observabilidade, CI/CD e operação de sistemas em escala.'],
        'backend'  => ['name' => '.NET & Backend',         'short' => 'Backend',    'ink' => '#fbbf24', 'deep' => '#2e2206', 'glyph' => 'λ',   'blurb' => 'APIs, performance, dados e o ecossistema .NET para back-end robusto.'],
        'front'    => ['name' => 'Frontend & Web',         'short' => 'Frontend',   'ink' => '#fb7185', 'deep' => '#33101a', 'glyph' => '</>',  'blurb' => 'Interfaces, performance no browser, design systems e a plataforma web moderna.'],
        'carreira' => ['name' => 'Carreira & Produtividade','short' => 'Carreira',  'ink' => '#60a5fa', 'deep' => '#0d1f3d', 'glyph' => '↗',   'blurb' => 'Crescimento, liderança técnica e hábitos para quem constrói software.'],
    ];
    return $cats[$slug] ?? ['name' => $slug, 'short' => $slug, 'ink' => '#7c5cff', 'deep' => '#1a1340', 'glyph' => '∑', 'blurb' => ''];
}

function rt_get_all_cats(): array {
    return ['ia', 'eng', 'cloud', 'backend', 'front', 'carreira'];
}

/* ============================================================
   THEME SETUP
   ============================================================ */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'script', 'style']);
    add_theme_support('custom-logo');

    register_nav_menus(['primary' => 'Menu Principal']);

    // Create default categories if they don't exist
    foreach (rt_get_all_cats() as $slug) {
        $cfg = rt_get_cat_config($slug);
        if (!term_exists($slug, 'category')) {
            wp_insert_term($cfg['name'], 'category', ['slug' => $slug]);
        }
    }
});

/* ============================================================
   ENQUEUE SCRIPTS & STYLES
   ============================================================ */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('rickytech-blog', get_stylesheet_uri(), [], '1.0.0');

    wp_enqueue_script(
        'rickytech-theme',
        get_template_directory_uri() . '/assets/js/theme.js',
        [],
        '1.0.0',
        true
    );

    wp_localize_script('rickytech-theme', 'rtConfig', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('rt_search'),
        'homeUrl'   => home_url('/'),
        'themeUri'  => get_template_directory_uri(),
    ]);
});

/* ============================================================
   CUSTOM POST META BOXES
   ============================================================ */
add_action('add_meta_boxes', function () {
    add_meta_box('rt_post_meta', 'Configurações do Artigo', 'rt_post_meta_box', 'post', 'side', 'high');
});

function rt_post_meta_box(WP_Post $post): void {
    $read_time = get_post_meta($post->ID, '_read_time', true);
    $featured  = get_post_meta($post->ID, '_featured', true);
    $lede      = get_post_meta($post->ID, '_lede', true);
    wp_nonce_field('rt_save_post_meta', 'rt_meta_nonce');
    ?>
    <p>
        <label style="display:block;font-weight:600;margin-bottom:4px">Lede (subtítulo do card)</label>
        <textarea name="rt_lede" rows="3" style="width:100%"><?php echo esc_textarea($lede); ?></textarea>
    </p>
    <p>
        <label style="display:block;font-weight:600;margin-bottom:4px">Tempo de leitura (min)</label>
        <input type="number" name="rt_read_time" value="<?php echo esc_attr($read_time ?: ''); ?>" min="1" max="60" style="width:80px">
    </p>
    <p>
        <label>
            <input type="checkbox" name="rt_featured" value="1" <?php checked($featured, '1'); ?>>
            <strong>Artigo em destaque</strong> (aparece no hero da home)
        </label>
    </p>
    <?php
}

add_action('save_post', function (int $post_id): void {
    if (!isset($_POST['rt_meta_nonce']) || !wp_verify_nonce($_POST['rt_meta_nonce'], 'rt_save_post_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $lede      = sanitize_textarea_field($_POST['rt_lede'] ?? '');
    $read_time = absint($_POST['rt_read_time'] ?? 0);
    $featured  = isset($_POST['rt_featured']) ? '1' : '';

    update_post_meta($post_id, '_lede', $lede);
    if ($read_time) update_post_meta($post_id, '_read_time', $read_time);
    if ($featured) {
        update_post_meta($post_id, '_featured', '1');
        // Remove featured from other posts
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_featured' AND meta_value = '1' AND post_id != %d",
            $post_id
        ));
    } else {
        delete_post_meta($post_id, '_featured');
    }
});

/* ============================================================
   AUTHOR META FIELDS
   ============================================================ */
add_action('show_user_profile', 'rt_author_meta_fields');
add_action('edit_user_profile', 'rt_author_meta_fields');

function rt_author_meta_fields(WP_User $user): void {
    $role      = get_user_meta($user->ID, '_role', true);
    $initials  = get_user_meta($user->ID, '_initials', true);
    $av_color  = get_user_meta($user->ID, '_av_color', true) ?: '#7c5cff';
    $x_handle  = get_user_meta($user->ID, '_x_handle', true);
    ?>
    <h3>Perfil Rickytech</h3>
    <table class="form-table">
        <tr><th><label>Cargo</label></th><td><input type="text" name="rt_role" value="<?php echo esc_attr($role); ?>" class="regular-text"></td></tr>
        <tr><th><label>Iniciais</label></th><td><input type="text" name="rt_initials" value="<?php echo esc_attr($initials); ?>" maxlength="3" style="width:60px"></td></tr>
        <tr><th><label>Cor do avatar</label></th><td><input type="color" name="rt_av_color" value="<?php echo esc_attr($av_color); ?>"></td></tr>
        <tr><th><label>Handle no X</label></th><td><input type="text" name="rt_x_handle" value="<?php echo esc_attr($x_handle); ?>" placeholder="@usuario" class="regular-text"></td></tr>
    </table>
    <?php
}

add_action('personal_options_update', 'rt_save_author_meta');
add_action('edit_user_profile_update', 'rt_save_author_meta');

function rt_save_author_meta(int $user_id): void {
    if (!current_user_can('edit_user', $user_id)) return;
    update_user_meta($user_id, '_role',     sanitize_text_field($_POST['rt_role']     ?? ''));
    update_user_meta($user_id, '_initials', sanitize_text_field($_POST['rt_initials'] ?? ''));
    update_user_meta($user_id, '_av_color', sanitize_hex_color($_POST['rt_av_color']  ?? '#7c5cff'));
    update_user_meta($user_id, '_x_handle', sanitize_text_field($_POST['rt_x_handle'] ?? ''));
}

/* ============================================================
   HELPER FUNCTIONS
   ============================================================ */

function rt_get_read_time(int $post_id): int {
    $meta = (int) get_post_meta($post_id, '_read_time', true);
    if ($meta > 0) return $meta;
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    return max(1, (int) ceil($word_count / 200));
}

function rt_get_lede(int $post_id): string {
    $lede = get_post_meta($post_id, '_lede', true);
    if ($lede) return $lede;
    return wp_trim_words(get_the_excerpt($post_id), 24, '');
}

function rt_get_post_cat_config(int $post_id): array {
    $cats = get_the_terms($post_id, 'category');
    if ($cats && !is_wp_error($cats)) {
        return rt_get_cat_config($cats[0]->slug);
    }
    return rt_get_cat_config('ia');
}

function rt_get_author_data(int $user_id): array {
    $user = get_userdata($user_id);
    if (!$user) return [];
    return [
        'id'       => $user->ID,
        'name'     => $user->display_name,
        'role'     => get_user_meta($user_id, '_role', true) ?: 'Autor',
        'initials' => get_user_meta($user_id, '_initials', true) ?: strtoupper(substr($user->display_name, 0, 2)),
        'av_color' => get_user_meta($user_id, '_av_color', true) ?: '#7c5cff',
        'x_handle' => get_user_meta($user_id, '_x_handle', true) ?: '',
        'bio'      => $user->description ?: '',
        'url'      => get_author_posts_url($user_id),
    ];
}

function rt_render_thumb(WP_Post $post, bool $show_cat = true, string $extra_class = ''): void {
    $cat_cfg   = rt_get_post_cat_config($post->ID);
    $ink       = esc_attr($cat_cfg['ink']);
    $deep      = esc_attr($cat_cfg['deep']);
    $slug      = esc_attr($post->post_name);
    $short     = esc_html($cat_cfg['short']);
    $glyph     = esc_html($cat_cfg['glyph']);
    $cls       = $extra_class ? " {$extra_class}" : '';
    echo "<div class=\"thumb{$cls}\" data-slug=\"{$slug}\" data-ink=\"{$ink}\" data-deep=\"{$deep}\" style=\"--cat-deep:{$deep};--cat-ink:{$ink}\">";
    echo '<canvas></canvas>';
    if ($show_cat) echo "<span class=\"thumb-cat\">{$short}</span>";
    echo "<span class=\"thumb-glyph\">{$glyph}</span>";
    echo '</div>';
}

function rt_render_avatar(array $author, int $size = 32): void {
    $color    = esc_attr($author['av_color'] ?? '#7c5cff');
    $initials = esc_html($author['initials'] ?? '??');
    $name     = esc_attr($author['name'] ?? '');
    $fs       = round($size * 0.38);
    echo "<div class=\"av\" style=\"width:{$size}px;height:{$size}px;font-size:{$fs}px;background:linear-gradient(135deg,{$color},color-mix(in oklch,{$color} 55%,#050507))\" title=\"{$name}\">{$initials}</div>";
}

function rt_render_icon(string $name, int $size = 18, bool $fill = false): void {
    $icons = [
        'search'     => 'M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.3-4.3',
        'sun'        => 'M12 3v2M12 19v2M5 5l1.5 1.5M17.5 17.5L19 19M3 12h2M19 12h2M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 100 8 4 4 0 000-8z',
        'moon'       => 'M21 12.8A9 9 0 1111.2 3a7 7 0 109.8 9.8z',
        'arrowRight' => 'M5 12h14M13 6l6 6-6 6',
        'arrowLeft'  => 'M19 12H5M11 18l-6-6 6-6',
        'bookmark'   => 'M6 4h12v16l-6-4-6 4V4z',
        'share'      => 'M16 6l-4-4-4 4M12 2v13M4 12v7a2 2 0 002 2h12a2 2 0 002-2v-7',
        'heart'      => 'M12 20s-7-4.6-9.5-9A4.8 4.8 0 0112 5a4.8 4.8 0 019.5 6c-2.5 4.4-9.5 9-9.5 9z',
        'clock'      => 'M12 7v5l3 2M12 21a9 9 0 100-18 9 9 0 000 18z',
        'check'      => 'M5 13l4 4L19 7',
        'twitter'    => 'M22 5.8c-.7.3-1.5.5-2.3.6a4 4 0 001.7-2.2c-.8.5-1.6.8-2.5 1a4 4 0 00-6.8 3.6A11.3 11.3 0 013 4.8a4 4 0 001.2 5.3c-.6 0-1.2-.2-1.7-.5a4 4 0 003.2 3.9c-.6.2-1.2.2-1.7.1a4 4 0 003.7 2.8A8 8 0 012 22a11.3 11.3 0 006.2 1.8c7.4 0 11.5-6.2 11.5-11.5v-.5c.8-.6 1.5-1.3 2.1-2z',
        'link'       => 'M10 13a5 5 0 007.5.5l3-3a5 5 0 00-7-7l-1.5 1.5M14 11a5 5 0 00-7.5-.5l-3 3a5 5 0 007 7L12 19',
        'rss'        => 'M5 19a1 1 0 100-2 1 1 0 000 2zM4 11a9 9 0 019 9M4 5a15 15 0 0115 15',
        'bolt'       => 'M13 2L4 14h7l-1 8 9-12h-7l1-8z',
        'settings'   => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z',
    ];
    $fill_attr = $fill ? 'currentColor' : 'none';
    $d = esc_attr($icons[$name] ?? '');
    echo "<svg width=\"{$size}\" height=\"{$size}\" viewBox=\"0 0 24 24\" fill=\"{$fill_attr}\" stroke=\"currentColor\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"{$d}\"></path></svg>";
}

function rt_rel_date(string $date): string {
    $ts   = strtotime($date);
    $now  = time();
    $days = (int) floor(($now - $ts) / 86400);
    if ($days <= 0) return 'hoje';
    if ($days === 1) return 'ontem';
    if ($days < 7)  return "há {$days} dias";
    if ($days < 30) return 'há ' . floor($days / 7) . ' sem';
    return date_i18n('d M Y', $ts);
}

function rt_get_popular_posts(int $limit = 5): array {
    return get_posts([
        'numberposts'    => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'comment_count',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);
}

function rt_get_related_posts(WP_Post $post, int $limit = 3): array {
    $cats = get_the_terms($post->ID, 'category');
    $cat_ids = $cats && !is_wp_error($cats) ? wp_list_pluck($cats, 'term_id') : [];
    return get_posts([
        'numberposts'    => $limit,
        'post_status'    => 'publish',
        'post__not_in'   => [$post->ID],
        'category__in'   => $cat_ids,
        'orderby'        => 'rand',
        'no_found_rows'  => true,
    ]);
}

function rt_get_featured_post(): ?WP_Post {
    $posts = get_posts([
        'numberposts'  => 1,
        'post_status'  => 'publish',
        'meta_key'     => '_featured',
        'meta_value'   => '1',
        'no_found_rows' => true,
    ]);
    if (!empty($posts)) return $posts[0];
    // fallback: most recent post
    $posts = get_posts(['numberposts' => 1, 'post_status' => 'publish']);
    return $posts[0] ?? null;
}

/* ============================================================
   AJAX SEARCH
   ============================================================ */
add_action('wp_ajax_rt_search',        'rt_ajax_search');
add_action('wp_ajax_nopriv_rt_search', 'rt_ajax_search');

function rt_ajax_search(): void {
    check_ajax_referer('rt_search', 'nonce');

    $q = sanitize_text_field($_GET['q'] ?? '');
    if (strlen($q) < 2) {
        wp_send_json([]);
    }

    $posts = get_posts([
        's'              => $q,
        'numberposts'    => 8,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ]);

    $results = [];
    foreach ($posts as $post) {
        $cat_cfg = rt_get_post_cat_config($post->ID);
        $author  = rt_get_author_data((int) $post->post_author);
        $results[] = [
            'title'     => $post->post_title,
            'url'       => get_permalink($post->ID),
            'slug'      => $post->post_name,
            'cat_short' => $cat_cfg['short'],
            'cat_ink'   => $cat_cfg['ink'],
            'cat_deep'  => $cat_cfg['deep'],
            'cat_glyph' => $cat_cfg['glyph'],
            'read_time' => rt_get_read_time($post->ID),
            'author'    => $author['name'] ?? '',
        ];
    }

    wp_send_json($results);
}

/* ============================================================
   NEWSLETTER FORM HANDLER
   ============================================================ */
add_action('wp_ajax_rt_newsletter',        'rt_ajax_newsletter');
add_action('wp_ajax_nopriv_rt_newsletter', 'rt_ajax_newsletter');

function rt_ajax_newsletter(): void {
    check_ajax_referer('rt_search', 'nonce');
    $email = sanitize_email($_POST['email'] ?? '');
    if (!is_email($email)) {
        wp_send_json_error('E-mail inválido.');
    }
    // TODO: integrate with Mailchimp / ConvertKit / custom table
    wp_send_json_success(['message' => 'Inscrito com sucesso!']);
}

/* ============================================================
   REST API — expõe campos customizados nos endpoints /wp-json/wp/v2/posts
   ============================================================ */
add_action('rest_api_init', function () {
    // Expõe _lede, _read_time, _featured como campos de leitura
    register_rest_field('post', 'rt_lede', [
        'get_callback' => fn($post) => rt_get_lede($post['id']),
        'schema'       => ['type' => 'string'],
    ]);
    register_rest_field('post', 'rt_read_time', [
        'get_callback' => fn($post) => rt_get_read_time($post['id']),
        'schema'       => ['type' => 'integer'],
    ]);
    register_rest_field('post', 'rt_featured', [
        'get_callback' => fn($post) => (bool) get_post_meta($post['id'], '_featured', true),
        'schema'       => ['type' => 'boolean'],
    ]);
    register_rest_field('post', 'rt_cat_config', [
        'get_callback' => fn($post) => rt_get_post_cat_config($post['id']),
        'schema'       => ['type' => 'object'],
    ]);
    register_rest_field('post', 'rt_author_data', [
        'get_callback' => function ($post) {
            $data = rt_get_author_data((int) get_post_field('post_author', $post['id']));
            // Remove URL interna — frontend monta via author ID
            return $data;
        },
        'schema' => ['type' => 'object'],
    ]);

    // Habilita CORS para o frontend React (ajuste o domínio em produção)
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        return $value;
    });
});

/* ============================================================
   DOCUMENT TITLE
   ============================================================ */
add_filter('document_title_separator', fn() => '·');
