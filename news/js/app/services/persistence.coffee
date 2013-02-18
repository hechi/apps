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
			@_initReqCount = 0
			@_loading.increase()

			# items can only be loaded after the all feeds and the active
			# feed is known
			loadItems: =>
				if @_initReqCount >= 2
					
					data =
						limit: @_config.itemBatchSize
						type: @_activeFeed.getType()
						id: @_activeFeed.getId()

					@_request.get 'news_items', {}, data, =>
						@_loading.decrease()

				else
					# hide or make feeds and folders visible based on their
					# unread count
					@_$rootScope.$broadcast('triggerHideRead')
					@_initReqCount += 1

			# feeds can only be loaded once all folders are known
			loadFeeds = =>
				@_request.get('news_feeds_active', {}, {}, loadItems)
				@_request.get('news_feeds', {}, {}, loadItems)
			
			@_request.get('news_settings_read')
			@_request.get('news_items_starred')
			@_request.get('news_folders', {}, {}, loadFeeds)



	return Persistence

