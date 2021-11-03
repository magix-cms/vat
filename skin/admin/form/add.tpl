<div class="row">
    <form id="add_vat" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add" method="post" class="validate_form add_form col-ph-12 col-lg-8 collapse in">
        <div class="row">
            <div class="col-ph-12 col-md-3">
                <div class="form-group">
                    <label for="percent_vat">{#percent_vat#|ucfirst}</label>
                    <div class="input-group">
                        <input type="text" id="percent_vat" name="vatData[percent_vat]" class="form-control" value="" placeholder="{#ph_percent_vat#|ucfirst}" />
                        <div class="input-group-addon"><span class="fas fa-percent"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="submit" class="col-ph-12 col-md-6">
            <button class="btn btn-main-theme pull-right" type="submit" name="action" value="add">{#save#|ucfirst}</button>
        </div>
    </form>
</div>