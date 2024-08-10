# linuxFeedbackProject
Secure Feedback Collection Website - This project we use 3 servers (A, B, and C)


## Project Overview
The Secure Feedback Project is a simple web application for collecting user feedback. It features a feedback form where users can submit their comments, ratings, and personal information. The application uses OKTA for user authentication and employs the ELK stack, Prometheus, and Grafana for monitoring. Load balancing is handled by HAProxy to ensure high availability.

## Features
- User authentication via OKTA.
- Feedback form for submitting first name, last name, rating, and comments.
- Feedback storage in a MySQL database.
- Display a confirmation message upon successful submission.
- Monitoring of application, and servers with Prometheus, and Grafana.
- Monitoring system logs with ELK stack
- Load balancing using HAProxy.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [Monitoring Setup](#monitoring-setup)
- [Log Monitoring Setup](#log-monitoring)
- [Contributing](#contributing)
- [License](#license)

## Installation

### Prerequisites
- Apache Web Server
- MySQL or MariaDB
- PHP
- OKTA account
- ELK stack (Elasticsearch, Logstash, Kibana)
- Node Exporter
- HAProxy
- Prometheus
- Grafana

### Steps
1. **Clone the repository**

    ```sh
    git clone https://github.com/mie2474/linuxFeedbackProject.git
    cd linuxFeedbackProject
    ```

2. **Set up the Database on Server (A and B)**

    - Install MySQL.
    ```sh
    sudo apt install mysql-server -y
    ```

    - Secure MySQL and follow the prompt
    ```sh
    sudo mysql_secure_installation
    ```
    - Login to the database
    ```sh
    sudo mysql
    ```
    - Create a database and user:
    ```sql
    CREATE DATABASE linuxPJsolo;
    CREATE USER 'claude'@'localhost' IDENTIFIED BY 'TeamClaude-6';
    GRANT ALL PRIVILEGES ON linuxPJsolo.* TO 'claude'@'localhost';
    FLUSH PRIVILEGES;
    ```
    - Login to MySql with the new user data, add password when prompted
    ```sh
    mysql -u claude -p
    ```
    - Create table in MySQL
    ```sql
    USE feedbackproject;
    CREATE TABLE feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        rating INT NOT NULL,
        comment TEXT NOT NULL,
        entered_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```


3. **Configure Apache on Server (A and B)**
    - Install Apache.
    ```sh
    sudo apt install apache2 -y
    sudo systemctl enable apache2
    sudo systemctl start apache2
    ```
    - Verify that Apache is running by typing `localhost` on your browser
    - A similar image would appear

    ![image](images/image.png)

    - To create a simple feedback form, copy the [index.html](index.html) in the linuxFeedback directory to `/var/www/html`. This will override the default html file.
    ```sh
    sudo cp index.html /var/www/html
    ```
    - Verify the updated file using by typing `localhost` on your browser.

    ![Feedback Form](images/image-1.png)

4. **Install and Configure PHP on Server (A and B)**
    - Ensure PHP is installed and configured correctly.
    - Install necessary PHP extensions:
    ```sh
    sudo apt install php libapache2-mod-php php-mysql php-curl -y
    ```
    - Create a file <info.php> in `/var/www/html` directory.
    ```sh
    sudo vim /var/www/html/info.php
    ```
    - Add the follow to `info.php`
    ```php
    <?php
    phpinfo();
    ?>
    ```
    - Verify PHP is running by opening your browser and type `localhost/info.php`
    - You will see something similar to below

    ![PHP](images/image-2.png)
    - Create PHP files to handle OKTA Authentication
        - Copy [callback.php](callback.php) to `/var/www/html` directory. 
        ```sh
        sudo cp callback.php /var/www/html # callback.php handles response from OKTA after user has successfully logged in.
        ```
        - Copy [index.php](index.php) to `/var/www/html` directory. 
        ```sh
        sudo cp index.php /var/www/html  # index.php serves as entry point of our application. It typically initiates the OKTA authentication process.
        ```
        - Copy [feedback.php](feedback.php) to `/var/www/htl` directory.
        ```sh
        sudo cp feedback.php /var/www/html # This sends response back to user - feedback successfully submitted
        ```
        - Copy [feedcount.php](feedcount.php) to `/var/www/htl` directory.
        ```sh
        sudo cp feedcount.php /var/www/html # This count the number of feedbacks recieved.
        ```

5. **Set up OKTA Authentication**
    - **NOTE: OKTA requires having domain in order to use the developer environmen or use free trial for 30 days**
    - Click https://www.okta.com/free-trial/ to register.
    - Follow OKTA documentation to set up a new application.
        -	Create a Developer OKTA Account
        -	Login to your Admin Console
        -	Navigate to **Applications** > **Applications**
        -	Click Create App Integration
        -	Choose OIDC - OpenID Connect and Web Application
        -	Set the Sign-in redirect URIs to `http://serverC-ip:80/callback.php.` # Update this to your loadbacer ip instead of localhost except you are running local VM
        -	Set the Sign-out redirect URIs to `http://serverC-ip:80/.` #Update this to your loadbacer ip instead of localhost except you are running local VM
        -	Click Save and make note of the **Client ID** and **Client Secret**.

    - Configure your application with the provided OKTA client ID and secret in the `/var/www/html` directory
    ```sh
    cd /var/www/html
    sudo apt install composer # It is required to manage and install necessaru PHP libraries and integrate with third party libraries.
    ```
    - Install JWT Verifier
    ```sh
    sudo composer require okta/jwt-verifier # it helps in verifying authentication tokens easily with PHP
    ```
    - Update the `callback.php` file in `/var/www/html/callback.php` to add your token url, clientID, and secret key


6. **Configure ELK Stack - to be install and configured on serverC**
    - Follow ELK documentation here [elkproject](elkproject.md)
        
7. **Configure HAProxy**
    - Install HAProxy on the serverC
    ```sh
    sudo apt install haproxy -y
    ```
    - Create a backup HAProxy file
    ```sh
    sudo cp /etc/haproxy/haproxy.cfg /etc/haproxy/haproxy.cfg.old
    ```
    - Update the configuration file by adding below and change the IP as needed.
    ```sh
    frontend main
            bind *:80
            stats uri /haproxy?stats # To check the health and statistics of servers
            default_backend app

    backend app
            balance         roundrobin # using roundrobin to balance the traffic evenly.
            server  server1 ServerA-ip:80   check # Change the ServerA-ip to your server ip - leave the port at 80 except you changed it.
            server  server2 ServerB-ip:80   check # Change the ServerA-ip to your server ip - leave the port at 80 except you changed it.

    ```

    - Validate haproxy.cfg after you modified the `/etc/haproxy/haproxy.cfg`.
    ```sh
    haproxy -c -f /etc/haproxy/haproxy.cfg
    ```
    - You should get the result below. If you have any errors, check you haproxy.cfg file.

    ![Haproxy Validation](images/image4.png)
   
    - Start and Enable HAProxy
    ```sh
    sudo systemctl enable haproxy
	sudo systemctl start haproxy
    ```
    - Open a web browser and type in `Loadbalancer-IP` you should see the content of your webserver. Assume this is `Tab A`
    - Open a new tab on your web browser to check the statistics and health of your webservers and type `http://loadbalancer-IP/haproxy?stats` Assume this is `Tab B`
    - You should see something similar.
    ![LB Statistics](<images/Screenshot 2024-07-31 165823.png>)
    - Refresh `Tab A` several times
    - Go to `Tab B` and check the `app` session `LbTot`

    ![haproxy](<images/Screenshot 2024-07-31 165823.png>)
    - NOTE: Always validate restart HAProxy after modifying the .cfg file
	```sh
    sudo systemctl restart haproxy
    ```
    - Refresh `Tab A` several times
    - Go to `Tab B` and check the `app` session `LbTot`

8. **Set up Node Exporter to scrape matrics - On serverA and ServerB**
    - Install and configure node exporter
    ```sh
    wget https://github.com/prometheus/node_exporter/releases/download/v1.8.2/node_exporter-1.8.2.linux-amd64.tar.gz
    ```
    - Extract the file to the current directory.
    ```sh
    tar -xvf node_exporter-1.8.2.linux-amd64.tar.gz
    ```
    - Move the Binary to `/usr/local/bin`
    ```sh
    sudo mv node_exporter-1.8.2.linux-amd64/node_exporter /usr/local/bin/
    ```
    - Create systemd service file
    ```sh
    sudo vim /etc/systemd/system/node_exporter.service
    ```
    - Add the following
    ```sh
    [Unit]
    Description=Node Exporter
    Wants=network-online.target
    After=network-online.target

    [Service]
    User=node_exporter
    ExecStart=/usr/local/bin/node_exporter

    [Install]
    WantedBy=default.target
    ```
    - Create a user for node_exporter
    ```sh
    sudo useradd -rs /bin/false node_exporter
    ```
    - Reload Systemd, Enable, and Start Node Exporter
    ```sh
    sudo systemctl daemon-reload
    sudo systemctl enable node_exporter
    sudo systemctl start node_exporter
    ```
    - Verify Node Exporter is running
    ```sh
    sudo systemctl status node_exporter
    ```
    - **Note: Node Exporter runs on port 9100**

9. **Set up Prometheus - On serverC**
    - Install Prometheus:
    ```sh
    wget https://github.com/prometheus/prometheus/releases/download/v2.53.1/prometheus-2.53.1.linux-amd64.tar.gz
    tar -xvf prometheus-2.53.1.linux-amd64.tar.gz
    sudo mv prometheus-2.53.1.linux-amd64 /usr/local/prometheus
    ```
    - Configure Prometheus
    ```sh
    sudo mkdir /etc/prometheus
    sudo mv /usr/local/prometheus/prometheus.yml /etc/prometheus/
    sudo cp /etc/prometheus/prometheus.yml /etc/prometheus/prometheus.yml.backup
    
    ```
    
    - Create a Systemd Service
        - Create a systemd service file
        ```sh
        sudo vim /etc/systemd/system/prometheus.service
        ```
        - Add the following content to the service file
            ```sh
            [Unit]
            Description=Prometheus
            Wants=network-online.target
            After=network-online.target

            [Service]
            User=prometheus
            Group=prometheus
            Type=simple
            ExecStart=/usr/local/prometheus/prometheus \
                --config.file=/etc/prometheus/prometheus.yml \
                --storage.tsdb.path=/var/lib/prometheus/ \
                --web.console.templates=/usr/local/prometheus/consoles \
                --web.console.libraries=/usr/local/prometheus/console_libraries

            [Install]
            WantedBy=multi-user.target
            ```
        - Create Prometheus user
        ```sh
        sudo useradd --no-create-home --shell /bin/false prometheus
        ```
        - Change ownership of Prometheus File
        ```sh
        sudo chown -R prometheus:prometheus /usr/local/prometheus
        sudo chown -R prometheus:prometheus /etc/prometheus
        ```
        
        
        - Create directory for logging
        ```sh
        sudo mkdir -p /var/lib/prometheus
        sudo chown -R prometheus:prometheus /var/lib/prometheus
        ```

    - Configure Prometheus to scrape metrics from your application. Edit the Prometheus configuration file (`/etc/prometheus/prometheus.yml`) to include your application endpoint:
    ```
    sudo vim /etc/prometheus/prometheus.yml
    ```
    - Update the target - add serverA-ip and serverB-ip
        ```yaml
        scrape_configs:
        - job_name: 'linux_feedback_project'
            static_configs:
            - targets: ['localhost:9090', 'serverA:9100','serverB:9100] # Add as many server - Ensure the servers have node exporter running on each. -targerts: ['localhost:9090','serverA:9100','serverb:9100']
        ```
    - Start Prometheus
    ```sh
    sudo systemctl daemon-reload 
    sudo systemctl enable prometheus
    sudo systemctl start prometheus
    sudo systemctl status prometheus
    ```
    - Verify on your browser by typing `http://serverC-ip:9090`
    - **NOTE:** Always restart Prometheus after updating (`/etc/prometheus/prometheus.yml`) file
    ```sh
    sudo systemctl restart prometheus
    ```
    - Verify the targets are added and running by navigating Click `Status` --> `Targets`
    ![Promethues](images/image-3.png)

10. **Set up Grafana - On serverC**
    - Install Grafana:
    ```sh
    sudo apt-get install -y adduser libfontconfig1 musl
    wget https://dl.grafana.com/enterprise/release/grafana-enterprise_11.1.3_amd64.deb
    sudo dpkg -i grafana-enterprise_11.1.3_amd64.deb
    ```
    - Start Grafana:
    ```sh
    sudo systemctl daemon-reload
    sudo systemctl enable grafana-server
    sudo systemctl start grafana-server
    ```
    - Access Grafana at `http://serverC-ip:3000` and log in with the default username (`admin`) and password (`admin`). Change the password if you want
    - Add Prometheus as a data source in Grafana:
      - Go to **Configuration > Data Sources > Add data source**.
      - Select Prometheus and set the URL to `http://serverC:9090`.
      - Click `Save & Test` you should get a message like below
      ![Grafana Save & Test](images/image-5.png)
      - Use Grafana to create dashboards to visualize the performance metrics in real-time.
        - On the dashboard, click `New` and select `Import`
        - Add `14731` to <Find and Import dashbaords>
        - Click `Load`
        - Update the name and select your data source (`prometheus`) then click `Import`
        - A dashboard like below should appear
        ![Grafana Dash](images/image-6.png)

## Workflow diagram
![Process-Diagram](images/Untitled%20Diagram.drawio.png)

## Usage
1. **Accessing the Application**
    - The application is accessible through Server C `http://serverC-ip`.
    - When you navigate to the HAProxy IP in your browser, it will open index.php for OKTA authentication.
    
2. **Submit Feedback**
    - Navigate to the feedback form, authenticate using OKTA, and submit your feedback.
    - Users can submit feedback through the form hosted on Servers A and B.
    - Once submitted, the feedback is stored in the database, and a confirmation message is displayed.

3. **Monitoring with Prometheus and Grafana**
    - Visit the Grafana web interface (typically http://Server_C_IP:3000)
    - View the dashboards to monitor the application's performance, server health, and network metrics.
    - Once submitted, the feedback is stored in the database, and a confirmation message is displayed.

## Project Structure
```plaintext
linuxFeedbackProject/
├── index.html
├── feedback.php
├── callback.php
├── README.md
├── feedcount.php
├── elkproject.md
├── schema.sql
└── index.php
