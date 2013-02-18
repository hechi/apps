###

ownCloud - News

@author Bernhard Posselt
@copyright 2012 Bernhard Posselt nukeawhale@gmail.com

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
License as published by the Free Software Foundation; either
version 3 of the License, or any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU AFFERO GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU Affero General Public
License along with this library.  If not, see <http://www.gnu.org/licenses/>.

###


angular.module('News').factory '_Persistence', ->
	
	class Persistence

		constructor: (@_request, @_loading, @_config, @_activeFeed,
						@_$rootScope) ->


		init: ->
			###
			Loads the initial data from the server
			###
			@_loading.increase()

			# items can only be loaded after the active feed is known
			@_request.get 'news_feeds_active', {}, {}, =>
				data =
					limit: @_config.itemBatchSize
					type: @_activeFeed.getType()
					id: @_activeFeed.getId()

				@_request.get 'news_items', {}, data, =>
					@_loading.decrease()

			
			@_request.get('news_folders', {}, {}, @_triggerHideRead)
			@_request.get('news_feeds', {}, {}, @_triggerHideRead)
			@_request.get('news_settings_read', @_triggerHideRead)
			@_request.get('news_items_starred', @_triggerHideRead)
			

		_trigerHideRead: ->
			@_$rootScope.$broadcast('triggerHideRead')


	return Persistence

