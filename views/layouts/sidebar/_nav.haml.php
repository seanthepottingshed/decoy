-$admin = app('decoy.user')
.navigation
	.top-level-nav
		-foreach($pages as $page)

			-if(!empty($page->children))
				.main-nav(class=$page->active?'active open':null)
					%a.top-level.parent
						-if($page->icon)
							%span.glyphicon(class="glyphicon-#{$page->icon}")
						!=$page->label

					.subnav
						-foreach($page->children as $child)
							-if (!empty($child->divider))
							-elseif($admin->can('read', $child->url))
								%a(href=$child->url class=$child->active?'active':null)
									-if($child->icon != 'default')
										%span.glyphicon(class="glyphicon-#{$child->icon}")
									=$child->label

			-elseif ($admin->can('read', $page->url))
				.main-nav(class=$page->active?'active':null)
					%a.top-level(href=$page->url)
						-if($page->icon)
							%span.glyphicon(class="glyphicon-#{$page->icon}")
						!=$page->label

		-# Settings folder
		:php
			$open = in_array(Request::segment(2), [
				'admins',
				'commands',
				'workers',
				'changes'
			]);
		.main-nav(class=$open?'active open':null)
			%a.top-level.parent
				%span.glyphicon.glyphicon-cog
				Settings

			.subnav

				-if($admin->can('read', 'admins'))
					%a(href=DecoyURL::action('Bkwld\\Decoy\\Controllers\\Admins@index') class=(Request::segment(2)=='admins'?'active':null)) Admins
				-else
					%a(href=$admin->getUserUrl() class=(Request::segment(2)=='admins'?'active':null)) Account

				-if($admin->can('read', 'changes'))
					%a(href=DecoyURL::action('Bkwld\\Decoy\\Controllers\\Changes@index') class=(Request::segment(2)=='changes'?'active':null)) Changes

				-if($admin->can('read', 'commands'))
					%a(href=route('decoy::commands') class=(Request::segment(2)=='commands'?'active':null)) Commands

				-if($admin->can('read', 'workers') && count(Bkwld\Decoy\Models\Worker::all()))
					%a(href=route('decoy::workers')  class=(Request::segment(2)=='workers'?'active':null)) Workers
