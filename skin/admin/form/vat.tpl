{extends file="catalog/{$smarty.get.controller}/edit.tpl"}
{block name="plugin:content"}
    <div class="row">
        <form id="edit_cat_vat" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$page.id_cat}&plugin={$smarty.get.plugin}" method="post" class="validate_form col-ph-12 col-md-6">
            <div class="row">
                <div class="col-ph-12 col-md-6">
                    <div class="form-group">
                        <label for="id_vat">{#vat#|ucfirst}</label>
                        <select name="id" id="id_vat" class="form-control">
                            <option value="">{#ph_vat#|ucfirst}</option>
                            {foreach $getVatCollection as $key}
                                <option value="{$key.id}" {if {$vat.id_vat} eq $key.id} selected{/if}>{$key.percent|ucfirst} %</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" id="id_cat" name="id_cat" value="{$page.id_cat}">
            <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
        </form>
    </div>
{/block}