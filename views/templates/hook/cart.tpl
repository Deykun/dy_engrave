  {if $engraver.enable}
  	<div id="engraver-list">
  	
		<button id="add-engrave" class="btn-engrave">Add engraving!</button>

		<div id="form-engrave" style="display: none;">
			<label for="engraveOnProduct">Pick item</label>
			<select id="engraveOnProduct">
				{foreach from=$engraver.products item=product}
					<option value="{$product.id}">{$product.name} - {$product.attributes}</option>
				{/foreach}
			</select>
			
			<label for="engraveThis">Text</label>
			<input id="engraveThis" type="text" placeholder="i<3U">
		</div>
	</div>
  {else}
  	{* You don't have any product with engrave feature *}
  {/if}