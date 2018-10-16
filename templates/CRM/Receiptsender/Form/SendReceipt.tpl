<<<<<<< HEAD
<div></div>
=======
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>
      {include file="CRM/Contribute/Form/Task.tpl"}
</div>

<table class="form-layout-compressed">
  <tr>
    <td>{$form.output.email_receipt.html}</td>
  </tr>
  <tr id="selectEmailFrom" style="display: none" class="crm-contactEmail-form-block-fromEmailAddress crm-email-element">
    <td class="label">{$form.from_email_address.label}</td>
    <td>{$form.from_email_address.html} {help id="id-from_email" file="CRM/Contact/Form/Task/Email.hlp" isAdmin=$isAdmin}</td>
  </tr>
  <tr>
    <td>{$form.output.pdf_receipt.html}</td>
  </tr>
</table>

<div class="spacer"></div>
<div class="form-item">
 {$form.buttons.html}
</div>
>>>>>>> e07cb6d90d54687fb26fff28ce58e911030f8423
