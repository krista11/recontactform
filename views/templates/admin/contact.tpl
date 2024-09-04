<div class="panel">
    <div class="panel-body">
        <table class="table-condensed">
            <tr>
                <th class="text-right">{l s='Form Type' d='Modules.Recontactform.Admin'}</th>
                <td>{$type}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='Name' d='Admin.Global'}</th>
                <td>{$contact->name}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='City' d='Admin.Global'}</th>
                <td>{$contact->city}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='Phone' d='Admin.Global'}</th>
                <td>{$contact->phone}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='Email' d='Admin.Global'}</th>
                <td>{$contact->email}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='Preliminary square footage of the room' d='Modules.Recontactform.Admin'}</th>
                <td>{$contact->square}</td>
            </tr>
            <tr>
                <th class="text-right">{l s='Image' d='Admin.Global'}</th>
                <td>
                    {if $image}
                        <img src="{$image}">
                    {/if}
                </td>
            </tr>
            <tr>
                <th class="text-right">{l s='Information' d='Admin.Global'}</th>
                <td>{$contact->info}</td>
            </tr>
        </table>

    </div>
    <div class="panel-footer">
        <a class="btn btn-default" id="re_contact_form_form_cancel_btn" onclick="javascript:window.history.back();">
            <i class="process-icon-cancel"></i>{l s='Back' d='Admin.Global'}
        </a>
    </div>
</div>