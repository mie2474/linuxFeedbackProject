---
runme:
  id: 01J4MMJXV14WQ3H2K50M8ZM0PY
  version: v3
---

**INSTALLING & cONFIGURING THE ELK STACK**

**Install Dependencies**

Installing Java: Elasticsearch and Logstash are Java-based applications and rely on the Java Virtual Machine (JVM) to run. To install Java, run the following command:

```sh {"id":"01J4MMX8WCV5BKP638E6QZB6M6"}
sudo apt update
```

```sh {"id":"01J4MMXVH1HVN0XB922SAXTH6Z"}
sudo apt-get install openjdk-17-jre -y
```

**Add Elastic Repositories (to enable access to all open-source software in the ELK slack)**

# Install GnuPG2 Package by running the following command

```sh {"id":"01J4MN0AEJC08QT1NBKKTT18XB"}

sudo apt-get install gnupg2 -y 
```

# Import GPG key for Elasticsearch packages

```sh {"id":"01J4MN2465G76FYSZ7H1QPR65B"}

wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
```

#Add the Elastic repository to your system’s repository list:

```sh {"id":"01J4MN4JW1P5VNW2DK0DDF7RSP"}

sudo sh -c 'echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" > /etc/apt/sources.list.d/elastic-7.x.list'
```

Install and Configure Elasticsearch:

#Before installing Elasticsearch, update the repositories by entering:

```sh {"id":"01J4MN6F1Y8ZRNVJ2WKXV2K979"}
sudo apt-get update
```

#Install Elasticsearch

```sh {"id":"01J4MN7RC1NXJ4M7ARHPNEKV31"}
sudo apt-get install elasticsearch
```

#Configure elasticsearch using nano. This allows us to control how the Elasticsearch behaves.

```sh {"id":"01J4MN9E47BD5XZYFX3KMJDC1K"}
sudo nano /etc/elasticsearch/elasticsearch.yml
```

Scroll down to find the two comments

```sh {"id":"01J4MNB5HB8RSS8HAHAK284ZDX"}
#network.host:192.168.0.1
#htpp.port:9200
```

To restrict access and, therefore, increase security, uncomment both, and replace the line that specifies network.host value with 0.0.0.0 like this:

```sh {"id":"01J4MNCKD8CNCE8P6CCWCC2190"}
network.host: 0.0.0.0
htpp.port:9200
```

Add discovery.type: single-node under the discovery section

```sh {"id":"01J4MNEE6JFFPHY26ZR8WSQ2CV"}
discovery.type: single-node
```

Save and exit the file once modified and restart the Elasticsearch service for the changes to take effect.

#Start Elasticsearch

```sh {"id":"01J4MNG6Z2VF4QZJ5BBHQHKHJF"}
sudo systemctl start elasticsearch.service
```

Enable Elasticsearch to start when the server boots

```sh {"id":"01J4MNH5WQZMJY41255D5RACNZ"}
sudo systemctl enable elasticsearch.service
```

#Test the configuration to ensure Elasticsearch is functional and listening on port 9200 using the command:

```sh {"id":"01J4MNK73YNGVTC61N9YZ2N4TT"}
curl -X GET "localhost:9200"
```

The output should be:

```sh {"id":"01J4MNMKVCR1CXGDP4KEJ6H0TR"}
{
  "name" : "testing",
  "cluster_name" : "elasticsearch",
  "cluster_uuid" : "hbjw0n24RZq_savOxfYjaw",
  "version" : {
    "number" : "7.17.23",
    "build_flavor" : "default",
    "build_type" : "deb",
    "build_hash" : "61d76462eecaf09ada684d1b5d319b5ff6865a83",
    "build_date" : "2024-07-25T14:37:42.448799567Z",
    "build_snapshot" : false,
    "lucene_version" : "8.11.3",
    "minimum_wire_compatibility_version" : "6.8.0",
    "minimum_index_compatibility_version" : "6.0.0-beta1"
  },
  "tagline" : "You Know, for Search"
}
```

**Install and Configure Kibana Dashboard**

#Please note that Kibana should only be installed after installing Elasticsearch to ensure that the components each product depends on are correctly in place. To install Kibana run the command:

```sh {"id":"01J4MNQDJTS57B0K6HJVRXW6CK"}
sudo apt install kibana
```

**Configure Kibana**

#Edit the configuration file for editing using the command:

```sh {"id":"01J4MNZ0BC8S99N3T7TN040FT6"}
sudo nano /etc/kibana/kibana.yml
```

Uncomment the following lines from the file removing ‘#’. 
The “localhost” and “your-hostname” should be changed to “0.0.0.0”

```sh {"id":"01J4MP0WV8EQW7BG06JY3RNV17"}
#server.port: 5601
#server.host: "your-hostname”
#elasticsearch.hosts: ["http://localhost:9200"]
```

The above-mentioned lines should look as follows:

```sh {"id":"01J4MP3259Y249FJVVSB00SCQZ"}
server.port: 5601
server.host: "0.0.0.0”
elasticsearch.hosts: ["http://0.0.0.0:9200"]
```

Save and close the file.

#Allow Traffic on Port 5601
#Create a firewall rule from the VM instance to allow http and https traffic on port 5601

#Enable the Kibana service by running the command:

```sh {"id":"01J4MP808R9KF1MH07P893V53T"}
sudo systemctl enable kibana
```

#Start the Kibana service by running the command:

```sh {"id":"01J4MP8V0YXT84SFMPXY898G26"}
sudo systemctl start kibana
```

#Test Kibana : Go to your browser and enter http://IP address:5601

**Install and Configure Logstash**

#Install Logstash with the command:

```sh {"id":"01J4MPEZDYN9S99N9AWXDV5P62"}
sudo apt-get install logstash
```

#Configuring Logstash.

All custom Logstash configuration files are stored in /etc/logstash/conf.d/. A Logstash pipeline has two required elements, input and output, and one optional element, filter. The input plugins consume data from a source, the filter plugins process the data, and the output plugins write the data to a destination.

While Beats can send data directly to Elasticsearch, it's often beneficial to use Logstash for data processing. Logstash provides greater flexibility, enabling you to collect data from various sources, transform it into a consistent format, and export it to different databases.

To  set up the Filebeat input create a configuration file named `02-beats-input.conf`:

```sh {"id":"01J4MPGS6YS8TED38KKN04ERVC"}
sudo nano /etc/logstash/conf.d/02-beats-input.conf
```

Then insert the following input configuration. This specifies a beats input that will listen on TCP port 5044

```sh {"id":"01J4MPJ4H2XYZN2PVY3PA45RFX"}
input {
beats {

port => 5044

}

}
```

Save and exit.

Next,  set up the Filebeat output configuration file called 30-elasticsearch-output.conf using the command:

```sh {"id":"01J4MPK6123C4Z184JWRW7H25Z"}
sudo nano /etc/logstash/conf.d/30-elasticsearch-output.conf
```

Insert the following output configuration. This configuration directs Logstash to store data from Beats into Elasticsearch. The data will be stored in an index named after the Beat used. Filebeat is the Beat used here.

```sh {"id":"01J4MPNRHH0HVRKTE8X061VKFK"}
output {
  if [@metadata][pipeline] {
	elasticsearch {
  	hosts => ["localhost:9200"]
  	manage_template => false
  	index => "%{[@metadata][beat]}-%{[@metadata][version]}-%{+YYYY.MM.dd}"
  	pipeline => "%{[@metadata][pipeline]}"
	}
  } else {
	elasticsearch {
  	hosts => ["localhost:9200"]
  	manage_template => false
  	index => "%{[@metadata][beat]}-%{[@metadata][version]}-%{+YYYY.MM.dd}"
	}
  }
}
```

Save and exit.

#Testing Logstash configuration by running the command:

```sh {"id":"01J4MPRTB6MD62M90W3WZVRG42"}
sudo -u logstash /usr/share/logstash/bin/logstash --path.settings /etc/logstash -t
```

If there are no syntax errors, your output will display "Config Validation Result: OK. Exiting Logstash" after a few seconds. 

#Starting and enabling Logstatsh

If the configuration is successful, start Logstash by running the command:

```sh {"id":"01J4MPVJ1TSP2XP1SYZ8664ABN"}
sudo systemctl start logstash
```

And enable Logstash using the command:

```sh {"id":"01J4MPWGG6HKBKYEDGBEBD9TKR"}
sudo systemctl enable logstash
```

**Installing and Configuring Filebeat**

The Elastic Stack utilizes several lightweight data shippers called Beats to collect data from various sources and transport it to Logstash or Elasticsearch. Filebeat, which collects and ships log files, is the Beat we are using here. Other Beats, such as Metricbeat, Packetbeat, and Auditbeat, serve different purposes for various types of data collection.

#Installing Filebeat using:

```sh {"id":"01J4MPY85EYRJ7GTAGEAPHNGPR"}
sudo apt install filebeat
```

#Configuring Filebeat  by opening the Filebeat configuration file:

```sh {"id":"01J4MPZ9S9C2YRG5YXXDHT3W84"}
sudo nano /etc/filebeat/filebeat.yml
```

Filebeat supports various output options, but in this setup, we will send events directly to Logstash for further processing. We won't need Filebeat to send data directly to Elasticsearch, so we will disable that output. To do this, locate the `output.elasticsearch` section and comment out the following lines by adding a `#` in front of them to look like:

```sh {"id":"01J4MQ03CQR7Q94MPP01HXADE6"}
#output.elasticsearch:
# Array of hosts to connect to.
#hosts: ["localhost:9200"]
```

Then, configure the output.logstash section. Uncomment the lines output.logstash: and hosts: ["localhost:5044"] by removing the # and replace “localhost” with “0.0.0.0”. This will configure Filebeat to connect to Logstash on your Elastic Stack server at port 5044, the port for which we specified a Logstash input earlier. the lines should like:

```sh {"id":"01J4MQ13QR1DJZVE1J6XD461KT"}
output.logstash:
# The Logstash hosts
hosts: ["0.0.0.0:5044"]
```

Save and exit.

#The functionality of Filebeat can be extended with Filebeat modules. Run the following command to enable filebeat modules

```sh {"id":"01J4MQ6HKHSH4DTK0M750F1XBE"}
sudo filebeat modules enable system
```

See a list of both enabled and disabled modules by running:

```sh {"id":"01J4MQ7NTJGJA0N89FKRY4YNVN"}
sudo filebeat modules list
```

Your output should look like:

```sh {"id":"01J4MQATDZEMDPCYX425WPNM9A"}
Enabled:

system

Disabled:

apache2

auditd

elasticsearch

icinga

iis

kafka

kibana

logstash

mongodb

mysql

nginx

osquery

postgresql

redis

traefik

...
```

#Set up the Filebeat ingest pipelines, which parse the log data before sending it through logstash to Elasticsearch by running the command:

```sh {"id":"01J4MQCX2MWDMPD7611CRQFXX1"}
sudo filebeat setup --pipelines --modules system
```

Load the index template into Elasticsearch. An Elasticsearch index is a collection  of documents with similar characteristics, identified by a name used to reference the index during various operations. The index template will be automatically    applied when a new index is created. Use the command

```sh {"id":"01J4MQEVFTDNGQKJ9KT5RN52MK"}
sudo filebeat setup --index-management -E output.logstash.enabled=false -E 'output.elasticsearch.hosts=["localhost:9200"]'
```

Output should show:

```sh {"id":"01J4MQFZTSQVCSPRVAFEDNH9WH"}
Index setup finished.
```

Filebeat comes with sample Kibana dashboards that help to visualize Filebeat data in Kibana. Before using these dashboards, create the index pattern and load the dashboards into Kibana.

During the loading process, Filebeat connects to Elasticsearch to verify version information. To load the dashboards when Logstash is enabled, the Logstash output must be disabled and the Elasticsearch output enabled:

```sh {"id":"01J4MQH492VKMZSCZS3SHD4KDP"}
sudo filebeat setup -E output.logstash.enabled=false -E output.elasticsearch.hosts=['localhost:9200'] -E setup.kibana.host=localhost:5601
```

Start and enable Filebeat by running:

```sh {"id":"01J4MQJHKQZ7T44BZBK79TARYP"}
sudo systemctl start filebeat
sudo systemctl enable filebeat

```

Query the Filebeat index To confirm that Elasticsearch is receiving the data using the  command:

```sh {"id":"01J4MQMD103C0Y114PHGM7PQ35"}
curl -XGET 'http://localhost:9200/filebeat-*/_search?pretty'

```

```sh {"id":"01J4MQMNY7HVMMKPMK3NB344NW"}
Output:

{
"took" : 4,

"timed_out" : false,

"_shards" : {

"total" : 2,

"successful" : 2,

"skipped" : 0,

"failed" : 0

},

"hits" : {

"total" : {

"value" : 4040,

"relation" : "eq"

},

"max_score" : 1.0,

"hits" : [

{

"_index" : "filebeat-7.17.2-2022.04.18",

"_type" : "_doc",

"_id" : "YhwePoAB2RlwU5YB6yfP",

"_score" : 1.0,

"_source" : {

"cloud" : {

"instance" : {

"id" : "294355569"

},

"provider" : "digitalocean",

"service" : {

"name" : "Droplets"

},

"region" : "tor1"

},

"@timestamp" : "2022-04-17T04:42:06.000Z",

"agent" : {

"hostname" : "elasticsearch",

"name" : "elasticsearch",

"id" : "b47ca399-e6ed-40fb-ae81-a2f2d36461e6",

"ephemeral_id" : "af206986-f3e3-4b65-b058-7455434f0cac",

"type" : "filebeat",

"version" : "7.17.2"

},
```

Explore the Kibana dashboard by going to the IP address