!= View::make('decoy::shared.form._header', $__data)->render()

%fieldset
	.legend=empty($item)?'New':'Edit'

	!= Former::text('first_name') 
	!= Former::text('last_name') 

	!= Former::text('email')
	-if (config('decoy.core.obscure_admin_password'))
		!= Former::password('password')
		!= Former::password('confirm_password')
	-else
		!= Former::text('password')->forceValue(empty($item)?Str::random(16):null)->placeholder(empty($item)?null:'Leave blank to prevent change')

	!= Former::image('image') 

	-if (app('decoy.auth')->can('grant', $controller) && ($roles = config('decoy.site.roles')) && !empty($roles))
		!= Former::radios('role')->radios(Bkwld\Library\Laravel\Former::radioArray($roles))
		!= View::make('decoy::admins._permissions', $__data)

	!= Former::checkbox('_send_email', 'Notify')->value(1)->text(empty($item)?'Send welcome email, including password':'Email '.$item->first_name.' with login changes')

	-# Create moderation actions
	-ob_start()
	-if (!empty($item) && app('decoy.auth')->can('grant', $controller))

		-# Disable admin
		-if (!$item->disabled())
			%a.btn.btn-warning.js-tooltip(title="Remove ability to login" href=URL::to(DecoyURL::relative('disable', $item->id)))
				%span.glyphicon.glyphicon-ban-circle
				Disable
		-else
			%a.btn.btn-warning.js-tooltip(title="Restore ability to login" href=URL::to(DecoyURL::relative('enable', $item->id)))
				%span.glyphicon.glyphicon-ban-circle
				Enable
	-$actions = ob_get_clean();

!= View::make('decoy::shared.form._footer', array_merge($__data, ['actions' => $actions]))->render()
