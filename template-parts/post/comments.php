<?php
if (post_password_required()) {
    return;
}

$comment_order = $_GET['comment_order'] ?? get_option('comment_order');
?>

<div id="PostSingle-comments" class="PostSingle-comments PostComments-root">

    <div class="Block-heading">
        <span aria-level="1" data-level="1">Comments</span>
    </div>

    <div class="PostComments-header">
        <h3 class="PostComments-count">
            <?php
            $count = get_comments_number();
            echo $count . ' ' . ($count === 1 ? 'Comment' : 'Comments');
            ?>
        </h3>

        <form method="get" class="Form-root Form-postComments">
            <?php
            foreach ($_GET as $key => $value) {
                if ($key !== 'comment_order') {
                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                }
            }
            ?>
            <label for="comment_order">Sort By:</label>
            <select id="comment_order" name="comment_order" onchange="this.form.submit()">
                <option value="desc" <?php selected($_GET['comment_order'] ?? '', 'desc'); ?>>Newest first</option>
                <option value="asc" <?php selected($_GET['comment_order'] ?? '', 'asc'); ?>>Oldest first</option>
            </select>
        </form>
    </div>

    <?php if (have_comments()): ?>
        <ul class="PostComments-list">
            <?php
            wp_list_comments([
                'style' => 'ol',
                'avatar_size' => 48,
                'short_ping' => true,
                'callback' => ['ThemeFunctions', 'comment_markup'],
                'type' => 'comment',
            ]);
            ?>
        </ul>
    <?php else: ?>
        <p class="no-comments">No comments yet.</p>
    <?php endif; ?>

    <div class="PostComments-footer">
        <h3 style="margin-bottom: 1rem;">Write your comment</h3>

        <?php
        comment_form([
            'title_reply' => '',
            'title_reply_to' => '',
            'label_submit' => 'Submit',
            'comment_notes_before' => '',
            'comment_notes_after' => '',

            'fields' => [
                'author' => '',
                'email' => '',
                'url' => '',
                'cookies' => '',
            ],

            'comment_field' => '
            <textarea id="comment" name="comment"
                placeholder="Write your comment here"
                rows="5"
                required></textarea>
        ',

            'submit_button' => '
            <button type="submit"
                class="CommentSubmit-btn Button-root Button-primary"
                data-variant="contained">
                <span>%1$s</span>
            </button>
        ',
        ]);
        ?>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commentForm = document.querySelector('#commentform');
        if (!commentForm) return;

        commentForm.addEventListener('submit', function (e) {
            const submitBtn = commentForm.querySelector('.CommentSubmit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('Button-loading');
            }
        });
    });
</script>