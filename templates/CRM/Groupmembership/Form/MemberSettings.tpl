{* HEADER *}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
</div>

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

<table>
  <tr><th>Apply to Membership Type</th><th>Use Relationship Type</th><th>Group Member Profile</th><th>Operations</th></tr>
  {foreach from=$existingNames item=group}
    <tr>
      {foreach from=$group item=elementName}
        <td class="content">{$form.$elementName.html}</td>
      {/foreach}
      <td class="operations">Delete</td>
    </tr>
  {/foreach}
</table>

<div class='new-setting'>
  <p>{ts}Add a new setting group:{/ts}</p>

  {foreach from=$elementNames item=elementName}
    <div class="crm-section">
      <div class="label">{$form.$elementName.label}</div>
      <div class="content">{$form.$elementName.html}</div>
      <div class="clear"></div>
    </div>
  {/foreach}
</div>
{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
