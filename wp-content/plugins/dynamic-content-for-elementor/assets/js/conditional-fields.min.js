"use strict";const getFieldsValues=(fields)=>{const values={};for(const id in fields){let $inputs=fields[id].inputs;if($inputs.length===1){let type=$inputs.attr('type');if(type==='checkbox'||type==='radio'){if($inputs.prop('checked')){values[id]=$inputs.val()}else{values[id]=''}}else{values[id]=$inputs.val()}}else{let checked=[];$inputs.each((_,e)=>{if(e.checked){checked.push(e.value)}})
if($inputs.attr('type')==="radio"){values[id]=checked[0]}else{values[id]=checked.length?checked:''}}}
return values};const getAllFields=($form,fieldIds)=>{const fields={};for(const id of fieldIds){const $group=$form.find('.elementor-field-group-'+id);if($group.length){fields[id]={wrapper:$group};let $field=$group.find(`[name=form_fields\\[${id}\\]]`);if($field.length){fields[id].inputs=$field}else{fields[id].inputs=$group.find(`[name=form_fields\\[${id}\\]\\[\\]]`)}}}
return fields}
const areAllFieldsDisabled=($groups)=>{for(const group of $groups){if(group.dataset.dceConditionsFieldStatus!=='inactive'){return!1}}
return!0}
const initializeStepsJumping=($form)=>{if(!$form.hasClass('dce-form-has-conditions')){return};let $steps=$form.find('.elementor-field-type-step');if($steps.length<3){return}
let steps=[];for(const step of $steps){const $step=jQuery(step);const $fieldGroups=$step.find('> .elementor-field-group');const $next=$step.find('.elementor-field-type-next button');const $previous=$step.find('.elementor-field-type-previous button');steps.push({nextButton:$next,previousButton:$previous,fieldGroups:$fieldGroups})}
for(let i=0;i<(steps.length-2);i++){const nextStep=steps[i+1];steps[i].nextButton.on('click',()=>{if(areAllFieldsDisabled(nextStep.fieldGroups)){nextStep.nextButton.trigger('click')}})}
for(let i=2;i<steps.length;i++){const prevStep=steps[i-1];steps[i].previousButton.on('click',()=>{if(areAllFieldsDisabled(prevStep.fieldGroups)){prevStep.previousButton.trigger('click')}})}}
const getOnFormChange=({fields,field_conditions,submit_conditions,$form,lang})=>{const $submitButtonWrapper=$form.find('.elementor-field-type-submit');const $submitButton=$submitButtonWrapper.find('button');const deactivateField=(id,disableOnly)=>{const $fieldInputs=fields[id].inputs;const $fieldWrapper=fields[id].wrapper;$fieldInputs.prop('disabled',!0)
$fieldWrapper[0].dataset.dceConditionsFieldStatus='inactive';if(!disableOnly){$fieldWrapper.hide()}}
const activateField=(id,disableOnly)=>{const $fieldInputs=fields[id].inputs;const $fieldWrapper=fields[id].wrapper;$fieldInputs.prop('disabled',!1);$fieldWrapper[0].dataset.dceConditionsFieldStatus='active';if($fieldInputs.data('hide')!=='yes'){$fieldWrapper.show()}}
const handleFieldConditions=(values)=>{for(const cond of field_conditions){let result;try{result=lang.evaluate(cond.condition,values)}catch(error){$form.prepend(`
<div class="error">
Conditional Fields Error (the error is on the conditions of field "<code>${cond.id}</code>"): ${error}
</div>`)}
const isActive=cond.mode==='show'?result:!result;if(!fields[cond.id]){console.warn('Conditional Fields, could not find field '+cond.id);continue}
if(isActive){activateField(cond.id,cond.disableOnly)}else{values[cond.id]='';deactivateField(cond.id,cond.disableOnly)}}}
const handleSubmitConditions=(values)=>{let hasDisableCondition=!1;let isDisabled=!1;let isHidden=!1;for(const cond of submit_conditions){let result;try{result=lang.evaluate(cond.expression,values)}catch(error){$form.prepend(`
<div class="error">
Conditional Fields Error (the error is on the validation condition <code>${cond}</code>"): ${error}
</div>`)}
if(cond.hide==='disable'){hasDisableCondition=!0}
if(!result){if(cond.hide==='hide'){isHidden=!0;$submitButtonWrapper.hide()}else if(cond.hide==='disable'){isDisabled=!0;$submitButton.prop('disabled',!0)}}}
if(!isHidden){$submitButtonWrapper.show()}
if(hasDisableCondition&&!isDisabled){$submitButton.prop('disabled',!1)}}
const onChange=()=>{const values=getFieldsValues(fields);handleFieldConditions(values);handleSubmitConditions(values)}
return onChange}
function initializeConditionalFields($form){const $outWrapper=$form.find('.elementor-form-fields-wrapper');$form.find('.dce-conditions-js-error-notice').remove();let field_conditions=$outWrapper.attr('data-field-conditions');let submit_conditions=$outWrapper.attr('data-submit-conditions');let fieldIds=$outWrapper.attr('data-field-ids');if(field_conditions===undefined&&submit_conditions===undefined){return}
$form.addClass('dce-form-has-conditions');const lang=new expressionLanguage.ExpressionLanguage(new expressionLanguage.ArrayAdapter);lang.register('in_array',(e)=>'false',(args,el,arr)=>{if(!Array.isArray(arr)){return arr===el}
return arr.includes(el)});lang.register('to_number',(e)=>'false',(args,str)=>{let n=+str;if(isNan(n)){n=0}
return n});fieldIds=JSON.parse(fieldIds);field_conditions=JSON.parse(typeof field_conditions==='string'?field_conditions:'[]');submit_conditions=JSON.parse(typeof submit_conditions==='string'?submit_conditions:'[]');const fields=getAllFields($form,fieldIds);let onFormChange=getOnFormChange({fields:fields,field_conditions:field_conditions,submit_conditions:submit_conditions,$form:$form,lang:lang,});onFormChange();$form.on('change',onFormChange)}
jQuery(window).on('elementor/frontend/init',function(){elementorFrontend.hooks.addAction('frontend/element_ready/form.default',initializeConditionalFields);elementorFrontend.hooks.addAction('frontend/element_ready/form.default',($scope)=>setTimeout(()=>initializeStepsJumping($scope),2000))})