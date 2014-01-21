{* HEADER *}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div><span style="font-weight: bold; font-size: 1.2em; padding-bottom: 0.5em; padding-top: 0.5em;">{$form.$elementName.label}</span></div>
    <div class="clear"></div>
    <div>{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>