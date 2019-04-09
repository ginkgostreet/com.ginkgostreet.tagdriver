<div id="help">
  <p>Help goes here.</p>
</div>

<div class="crm-block crm-form-block">

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <table class="form-layout">
    <tr>
      <td class="label">{$form.tagdriver_x.label}</td>
      <td>
        {$form.tagdriver_x.html}
        <div class="description">Contacts with the selected tag will automatically have a user account created for them.</div>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.tagdriver_tb.label}</td>
      <td>
        {$form.tagdriver_tb.html}
        <div class="description">If multiple contacts with the same email address are tagged for automatic user account creation, the one with this tag wins.</div>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.tagdriver_z.label}</td>
      <td>
        {$form.tagdriver_z.html}
        <div class="description">After a user account is automatically created, the contact will receive this tag.</div>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.tagdriver_y.label}</td>
      <td>
        {$form.tagdriver_y.html}
        <div class="description">Assign this tag to a contact to have a password reset link sent to them.</div>
      </td>
    </tr>
    <tr>
      <td class="label">{$form.tagdriver_pattern.label}</td>
      <td>
        {$form.tagdriver_pattern.html}
        <div class="description">Pattern for username selection. All contact tokens are supported.</div>
      </td>
    </tr>
  </table>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

</div>
