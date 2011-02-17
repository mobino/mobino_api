Radiant Extension for Mobino
============================
v 0.1, 17.2.2010, (c) Mobino SA

This extension handles the embedding of the mobino widget in a Radiant site.

Prerequisites
-------------

'Signum' gem installed

----
gem install signum
----

Usage
-----

It can be used as follows in a Radiant page part:

----
<div id="mobino">
  <script src="http://mobino.com/api/mobino.js"></script>
  <script>
    MobinoLoader.initializer({'env':'production', 'lang': 'fr', 'coutry': 'FR'});
    MobinoLoader.createPayment(<r:mobino amount="10.00" merchant_id="5" reference_number="12345" env="production" />);
  </script>
</div>
----

API Key and Secret are configured in the file +config/settings.yml+.

The following attributes can be passed to the mobino tag:

+amount+:: The amount of the transaction
+reference_number+:: Your reference number for this transaction
+env+:: The environment against which the transaction should be executed.
Currently available: +production+ and +staging+
You must use the same setting at the JavaScript and the Tag.
+transaction_type+:: Either a +regular+ or a +gift+ transaction (see main Readme for difference)


