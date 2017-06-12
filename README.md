# php-cassel
N-Tier client-side cascading selects (import from my old phpclasses days)

Last updated 2005-07-22

Originally created some time around 2005, this was to solve the
common problem of cascading client &lt;select&gt; tags, eg, if a page
needed to have a selection of:

- country
    - province
        - city
            - town

then this could be loaded up with the data required and
it would use Javascript at the client to narrow down select lists
as the user made higher-up selections. Not too exciting now, but
it made life much easier then.
