<div class="content-tags">
    <div class="note">
        <h3>Общие заметки</h3>
        <form method="POST" class="detwork-control">
            <textarea name="note" class="editor form-control data-control" rows="10"><?=get_option('admin_notes');?></textarea>
            <?php if(check_rule('admin_settings')): ?>
                <hr />
                <button class="btn btn-primary input-control button-control" data-action="update_note"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
            <?php endif; ?>
        </form>
    </div>
</div>