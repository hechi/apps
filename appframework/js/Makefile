firefox_bin=/usr/bin/firefox
chrome_bin=/usr/bin/chromium
coffee=$(CURDIR)/node_modules/coffee-script/bin/coffee
grunt=$(CURDIR)/node_modules/grunt-cli/bin/grunt
phantomjs=$(CURDIR)/node_modules/phantomjs/bin/phantomjs

all: compile

deps:
	cd $(CURDIR)/
	npm install --deps
	cd ..

watch: compile
	$(coffee) --compile --watch --output $(CURDIR)/build/app $(CURDIR)/app/ & \
	$(coffee) --compile --watch --output $(CURDIR)/build/tests $(CURDIR)/tests/ & \
	$(grunt) --config $(CURDIR)/Gruntfile.coffee run

testacular: deps
	export CHROME_BIN=$(chrome_bin) && export FIREFOX_BIN=$(firefox_bin) && \
	$(grunt) --config $(CURDIR)/Gruntfile.coffee testacular

phpunit:
	$(grunt) --config $(CURDIR)/Gruntfile.coffee testphp	

compile: deps
	mkdir -p $(CURDIR)/build/app
	mkdir -p $(CURDIR)/build/tests
	$(coffee) --compile --output $(CURDIR)/build/app $(CURDIR)/app/
	$(coffee) --compile --output $(CURDIR)/build/tests $(CURDIR)/tests/
	$(grunt) --config $(CURDIR)/Gruntfile.coffee compile

test: deps compile
	export PHANTOMJS_BIN=$(phantomjs) && \
	$(grunt) --config $(CURDIR)/Gruntfile.coffee ci

clean:
	rm -rf $(CURDIR)/build
	rm -rf $(CURDIR)/test-results.xml
