 
STAN=vendor/bin/phpstan --memory-limit=1024M
STAN_LEVEL=5

.PHONY: analyze
analyze:
	$(STAN) analyse -l $(STAN_LEVEL) src