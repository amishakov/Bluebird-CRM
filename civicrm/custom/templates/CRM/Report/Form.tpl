{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{*NYSS include additional reports css*}
{literal}
<link type="text/css" rel="stylesheet" media="screen,projection" href="/sites/default/themes/Bluebird/css/reportsCivicrm.css" />
{/literal}

{if $outputMode neq 'print'}
  {include file="CRM/common/crmeditable.tpl"}
{/if}
{* this div is being used to apply special css *}
{if $section eq 1}
  <div class="crm-block crm-content-block crm-report-layoutGraph-form-block">
    {*include the graph*}
    {include file="CRM/Report/Form/Layout/Graph.tpl"}
  </div>
{elseif $section eq 2}
  <div class="crm-block crm-content-block crm-report-layoutTable-form-block">
    {*include the table layout*}
    {include file="CRM/Report/Form/Layout/Table.tpl"}
	</div>
{else}
  {*NYSS*}
  {*include actions*}
  {include file="CRM/Report/Form/Actions.tpl"}

  {if $criteriaForm OR $instanceForm OR $instanceFormError}
    <div class="crm-block crm-form-block crm-report-field-form-block">
      {include file="CRM/Report/Form/Fields.tpl"}
    </div>
  {/if}
    
  <div class="crm-block crm-content-block crm-report-form-block">
    {*Statistics at the Top of the page*}
    {include file="CRM/Report/Form/Statistics.tpl" top=true}

    {*include the graph*}
    {include file="CRM/Report/Form/Layout/Graph.tpl"}

    {*include the table layout*}
    {include file="CRM/Report/Form/Layout/Table.tpl"}
    <br />
    {*Statistics at the bottom of the page*}
    {include file="CRM/Report/Form/Statistics.tpl" bottom=true}

    {include file="CRM/Report/Form/ErrorMessage.tpl"}
  </div>
{/if}
{if $outputMode == 'print'}
  <script type="text/javascript">
    window.print();
  </script>
{/if}

{*NYSS 6440*}
{include file="CRM/Report/updateConfirm.tpl"}

{literal}
<script type="text/javascript">
  cj('input[name=crmPID_B]').keyup(function(){
    //console.log('PID B: ', cj(this).val());
    //console.log('PID: ', cj('input[name=crmPID]').val());

    cj('input[name=crmPID]').val(cj(this).val());
  });
</script>
{/literal}