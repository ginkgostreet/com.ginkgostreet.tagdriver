<div id="help">
  <p>Tag Driver is a CiviCRM Utility for accomplishing tasks in bulk by adding a tag to one or more contact records. To use, search for a contact or a group of contacts, and add a Tag Driver tag to them. When the next Tag Driver job runs, the associated actions will be performed automagically. Actions that are currently supported are:</p>
  <ul>
    <li><strong>Create a CMS user account:</strong> Adding the "Tag Driver: Create CMS Account" tag to a contact record will create a CMS user account for that contact on the next run of the TagDriver job. <em>Note:</em> Contacts that have had a CMS user account created for them by the Tag Driver extension will have the "Tag Driver: User Account" tag assigned to them.</li>
    <li><strong>Reset CMS Password:</strong> Adding the "Tag Driver: Reset CMS Password" will have a password reset email sent to the contact automatically on the next Tag Driver job run.</li>
  </ul>
</div>

<div class="crm-block crm-form-block">

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <table class="form-layout">
    <tr>
      <td class="label">{$form.tagdriver_tb.label}</td>
      <td>
        {$form.tagdriver_tb.html}
        <div class="description">If multiple contacts with the same email address are tagged for automatic user account creation, the one with this tag wins.</div>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.tagdriver_pattern.label}</td>
      <td>
        {$form.tagdriver_pattern.html}
        {literal}
        <div class="description">Pattern for username selection. All CiviCRM tokens are supported and additional tokens for {contact.first_initial} and {contact.last_initial} have been added.</div>
        {/literal}
      </td>
    </tr>
  </table>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

</div>
