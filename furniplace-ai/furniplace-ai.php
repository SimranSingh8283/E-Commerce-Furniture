<?php
/**
 * Plugin Name: FurniPlace AI (Gemini)
 * Description: View furniture in customer room using Gemini AI
 * Version: 1.0
 */

if (!defined('ABSPATH'))
    exit;

/* -------------------------------------------------
 * ADMIN SETTINGS (Gemini API Key)
 * ------------------------------------------------- */
add_action('admin_menu', function () {
    add_options_page(
        'FurniPlace AI',
        'FurniPlace AI',
        'manage_options',
        'furniplace-ai',
        'furniplace_ai_settings_page'
    );
});

function furniplace_ai_settings_page()
{
    ?>
    <div class="wrap">
        <h1>FurniPlace AI Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('furniplace_ai');
            do_settings_sections('furniplace_ai');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {
    register_setting('furniplace_ai', 'furniplace_gemini_api_key');

    add_settings_section(
        'furniplace_ai_section',
        'Gemini Configuration',
        null,
        'furniplace_ai'
    );

    add_settings_field(
        'furniplace_gemini_api_key',
        'Gemini API Key',
        function () {
            $key = esc_attr(get_option('furniplace_gemini_api_key'));
            echo "<input type='text' name='furniplace_gemini_api_key' value='{$key}' style='width:400px'>";
        },
        'furniplace_ai',
        'furniplace_ai_section'
    );
});

/* -------------------------------------------------
 * FRONTEND ASSETS
 * ------------------------------------------------- */
add_action('wp_enqueue_scripts', function () {
    if (!is_product())
        return;

    wp_enqueue_style(
        'furniplace-css',
        plugin_dir_url(__FILE__) . 'assets/furniplace.css'
    );

    wp_enqueue_script(
        'furniplace-js',
        plugin_dir_url(__FILE__) . 'assets/furniplace.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('furniplace-js', 'FurniPlaceAI', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'product_id' => get_the_ID()
    ]);
});

/* -------------------------------------------------
 * PRODUCT PAGE BUTTON + POPUP
 * ------------------------------------------------- */
add_action('wp_footer', function () {
    if (!is_product())
        return;
    ?>
    <!-- FurniPlace Modal -->


    <div id="furniplace-modal" data-lenis-prevent style="display:none;">
        <div class="furniplace-modal-content">
            <div class="Container-root">

                <div class="furniplace-modal-header">
                    <div class="Block-heading">
                        <span data-level="1" aria-level="1">Upload Your Room Image</span>
                    </div>
                    <button class="Button-root Button-icon-start" id="furniplace-close">
                        <iconify-icon icon="line-md:close"></iconify-icon> <span>Close</span>
                    </button>
                </div>

                <div class="furniplace-modal-body">
                    <div class="Flex-root Flex-wrap">
                        <div class="Col-root Col-lg-6">
                            <input type="file" id="furniplace-room" accept="image/*">

                            <div class="instruction-section">
                                <label for="furniplace-instruction">
                                    Placement Instruction (Optional)
                                </label>
                                <textarea id="furniplace-instruction"
                                    placeholder="e.g., Place the chair near the window on the left side..."></textarea>
                            </div>

                            <button id="furniplace-generate" class="Button-root Button-primary" data-variant="contained">
                                Generate Preview
                            </button>
                        </div>

                        <div class="Col-root Col-lg-6">
                            <div id="furniplace-result"></div>
                        </div>
                    </div>
                </div>

                <div id="furniplace-loading" style="display:none;">
                    <div class="furniplace-spinner"></div>
                    <p>AI is placing the furniture in your room…</p>
                </div>
            </div>
        </div>
    </div>


    <script>
        jQuery(function ($) {

            // ✅ EXACT selector from your theme
            const placement = document.querySelector(
                '.Product-thumbnail'
            );

            if (!placement) {
                console.warn('FurniPlace: Placement not found');
                return;
            }

            // Prevent duplicate AI button
            const aiBtn = document.createElement('button');
            aiBtn.type = 'button';
            aiBtn.className = 'furniplace-ai-btn Button-root Button-primary Button-icon-start';
            aiBtn.setAttribute("data-variant", "contained")
            aiBtn.innerHTML = `<iconify-icon style="font-size: 1.5rem;" icon="ph:cube-focus-light"></iconify-icon> <span>Upload To Place Item</span>`;

            // Match theme styling
            const style = getComputedStyle(placement);

            // Insert button
            placement.insertAdjacentElement('afterbegin', aiBtn);

        });
    </script>
    <?php
});


/* -------------------------------------------------
 * AJAX HANDLER (Gemini Call)
 * ------------------------------------------------- */
add_action('wp_ajax_furniplace_generate', 'furniplace_generate');
add_action('wp_ajax_nopriv_furniplace_generate', 'furniplace_generate');

function furniplace_generate()
{



    $apiKey = get_option('furniplace_gemini_api_key');
    if (!$apiKey) {
        wp_send_json_error('Gemini API key not set');
    }

    $product_id = intval($_POST['product_id']);
    $roomBase64 = $_POST['roomBase64'] ?? '';
    $roomMime = $_POST['roomMime'] ?? '';
    $userInstruction = trim($_POST['instruction'] ?? '');

    if (!$roomBase64 || !$roomMime) {
        wp_send_json_error('Room image missing');
    }

    /* PRODUCT IMAGE */
    $img_id = get_post_thumbnail_id($product_id);
    if (!$img_id) {
        wp_send_json_error('Product image missing');
    }

    $img_path = get_attached_file($img_id);
    $furnitureBase64 = base64_encode(file_get_contents($img_path));
    $furnitureMime = mime_content_type($img_path);



    /* BASE INSTRUCTION (STRICT – SAME AS DEMO) */
    $baseInstruction = <<<PROMPT
Task:
You are given two images.

Image 1: A real, existing piece of furniture (reference object).
Image 2: A room interior.

Objective:
Insert the exact same furniture from Image 1 into the room shown in Image 2.

Hard Requirements (Must Follow):
Preserve the furniture 100% identical to Image 1.
Do NOT change design, shape, proportions, materials, textures, or colors.
Do NOT reinterpret or redesign the furniture.

Allowed Transformations:
Resize uniformly
Rotate
Adjust perspective
Adjust lighting and shadows only

Quality Goal:
The final image must look like a real photograph.
PROMPT;

    /* MERGE USER INSTRUCTION */
    if (!empty($userInstruction)) {
        $finalInstruction = $baseInstruction . "\n\nUser Placement Instruction:\n" . $userInstruction;
    } else {
        $finalInstruction = $baseInstruction;
    }




    /* GEMINI PAYLOAD (MATCHING DEMO ORDER) */
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'inlineData' => [
                            'data' => $furnitureBase64,
                            'mimeType' => $furnitureMime
                        ]
                    ],
                    [
                        'inlineData' => [
                            'data' => $roomBase64,
                            'mimeType' => $roomMime
                        ]
                    ],
                    [
                        'text' => $finalInstruction
                    ]
                ]
            ]
        ]
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-image:generateContent?key={$apiKey}";

    $response = wp_remote_post($url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($payload),
        'timeout' => 120
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    }




    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['candidates'][0]['content']['parts'])) {
        wp_send_json_error('No response from AI');
    }

    foreach ($body['candidates'][0]['content']['parts'] as $part) {
        if (isset($part['inlineData'])) {
            wp_send_json_success(
                'data:' . $part['inlineData']['mimeType'] .
                ';base64,' . $part['inlineData']['data']
            );
        }
    }

    wp_send_json_error('No image generated');
}

