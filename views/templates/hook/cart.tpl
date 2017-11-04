  {if $engraver.enable}
  	<div id="engraver-list">
  	
		<button id="add-engrave" class="btn-engrave">Add engraving!</button>

		<div id="form-engrave" class="close">
			<div>
				<h2>{l s='Engraver' d='Modules.Engrave.Admin'}</h2>
			
				<div>
					<span id="price-engrave" data-base="{$engraver.price.base}">{$engraver.price.base|string_format:"%.2f"}</span> {$currency.sign}
				</div>
				
				<div id="product-engrave">
					<h3>{l s='Pick item' d='Modules.Engrave.Admin'}</h3>
					
					{foreach from=$engraver.products item=product name=products}
						<label for="en-product{$smarty.foreach.products.iteration}">
							<input type="radio" id="en-product{$smarty.foreach.products.iteration}" name="contact" value="{$product.id}">
							
							<div>
								{if !empty($product.cover_url)}
									<img class="img-fluid" src="{$product.cover_url}" alt="{$product.name}">
								{/if}
								<h4>{$product.name}</h4>
								<p>{$product.attributes}</p>
							</div>														
						</label>
					
					{/foreach}
					
				</div>
				<div>
					<label for="text-engrave">{l s='Text' d='Modules.Engrave.Admin'}</label>
					<input id="text-engrave" type="text" placeholder="i <3 u" maxlength="{(count($engraver.price.combinations)-1)}">
				</div>
				
				<ul id="impact-engrave" style="display:none;">
					{foreach from=$engraver.price.combinations item=combination}
						<li data-length="{$combination.value}" data-impact="{$combination.price_impact}">
							{$combination.value} z. - {$combination.price_impact}
						</li>
					{/foreach}
					
				</ul>
				
				{*<h3>Dev</h3>
				<pre>
					 {$currency.sign} 
				</pre>*}
			</div>
		</div>
	</div>
  {else}
  	{* You don't have any product with engrave feature *}
  {/if}