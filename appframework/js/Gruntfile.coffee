###

ownCloud - App Framework

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


module.exports = (grunt) ->
	
	grunt.loadNpmTasks('grunt-contrib-coffee')
	grunt.loadNpmTasks('grunt-contrib-concat')
	grunt.loadNpmTasks('grunt-contrib-watch')
	grunt.loadNpmTasks('grunt-coffeelint')
	grunt.loadNpmTasks('grunt-wrap');
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.loadNpmTasks('gruntacular');

	grunt.initConfig
	
		meta:
			pkg: grunt.file.readJSON('package.json')
			version: '<%= meta.pkg.version %>'
			banner: '/**\n' +
				' * <%= meta.pkg.description %> - v<%= meta.version %>\n' +
				' *\n' +
				' * Copyright (c) <%= grunt.template.today("yyyy") %> - ' +
				'<%= meta.pkg.author.name %> <<%= meta.pkg.author.email %>>\n' +
				' *\n' +
				' * This file is licensed under the Affero General Public License version 3 or later.\n' +
				' * See the COPYING-README file\n' +
				' *\n' + 
				' */\n\n' +
				' /* Generated with Coffee-Script */\n'
			build: 'build/'
			production: 'public/'

		concat:
			app: 
				options:
					banner: '<%= meta.banner %>\n'
					stripBanners: 
						options: 'block'
				src: [
						'<%= meta.build %>app/app.js'
						'<%= meta.build %>app/directives/*.js'
						'<%= meta.build %>app/services/*.js'
					]
				dest: '<%= meta.production %>app.js'
		wrap:
			app:
				src: '<%= meta.production %>app.js'
				dest: ''
				wrapper: [
					'(function(angular, $, OC, oc_requesttoken){\n\n'
					'\n})(window.angular, jQuery, OC, oc_requesttoken);'
				]

		coffee: 
			app:
				expand: true
				cwd: 'app/'
				src: ['**/*.coffee']
				dest: '<%= meta.build %>app/'
				ext: '.js'
			unit:
				expand: true
				cwd: 'tests/'
				src: ['**/*.coffee']
				dest: '<%= meta.build %>tests/'
				ext: '.js'				

		coffeelint:
			app: [
				'app.coffee'
				'services/*.coffee'
				'directives/*.coffee'
			]
		coffeelintOptions:
			'no_tabs':
				'level': 'ignore'
			'indentation':
				'level': 'ignore'
			'no_trailing_whitespace':
				'level': 'warn'

		watch: 
			## Use later when watch can finally update single files
			#app: 
			#	files: [
			#		'app/**/*.coffee'
			#		'tests/directives/*.coffee'
			#		'tests/services/*.coffee'
			#		'tests/controllers/*.coffee'
			#		'tests/filters/*.coffee'
			#	]
			#	tasks: 'compile'
			#testacular:
			#	files: [
			#		'<%= meta.build %>app/**/*.js'
			#		'<%= meta.build %>tests/**/*.js'
			#	]
			#	tasks: ['testacular:unit:run', 'concat']
			concat:
				files: [
					'<%= meta.build %>app/**/*.js'
					'<%= meta.build %>tests/**/*.js'
				]
				tasks: 'concat'		
			lint: 
				files: [
					'app/**/*.coffee'
					'tests/directives/*.coffee'
					'tests/services/*.coffee'
					'tests/filters/*.coffee'
				]
				tasks: 'coffeelint'
			phpunit:
				files: '../**/*.php'
				tasks: 'phpunit'

		
		testacular: 
			unit: 
				configFile: 'config/testacular.conf.js'
			continuous:
				configFile: 'config/testacular.conf.js'
				singleRun: true
				browsers: ['PhantomJS']
				reporters: ['progress', 'junit']
				junitReporter:
					outputFile: 'test-results.xml'


		phpunit:
			classes:
				dir: '../tests'
			options:
				colors: true


	grunt.registerTask('run', ['watch'])
	grunt.registerTask('compile', [
			'concat:app'
			'wrap'
		]
	)

	grunt.registerTask('ci', [
			'testacular:continuous'
		]
	)

	grunt.registerTask('testphp', ['watch:phpunit'])