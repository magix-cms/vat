<div class="row">
    <form id="edit_vat" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$page.id_vat}" method="post" class="validate_form edit_form col-ph-12 col-lg-8">
        <div class="row">
            <div class="col-ph-12 col-md-3">
                <div class="form-group">
                    <label for="percent_vat">{#percent_vat#|ucfirst}</label>
                    <div class="input-group">
                        <input type="text" id="percent_vat" name="vatData[percent_vat]" class="form-control" value="{$page.percent_vat}" placeholder="{#ph_percent_vat#|ucfirst}" />
                        <div class="input-group-addon"><span class="fas fa-percent"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="id_vat" name="id" value="{$page.id_vat}">
        <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </form>
</div>