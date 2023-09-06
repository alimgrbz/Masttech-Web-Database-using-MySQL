# Masttech-Web-Database

This project is a Web Application development for Masttech's product database, for both customers and staffs.

## Features
- For staff members, it offers a product entry and visualization interface.
- For customers, it offers a product configuration model with filtering.

## Technology Stack
- The Web Application uses **MySQL through phpMyAdmin**.
  - [for more information](https://www.phpmyadmin.net/)
- The local machine is used as a server and acts as a development server running **Apache, XAMPP**.
  - [More about XAMPP](https://www.apachefriends.org/)

## Database Design

The database is designed using **MySQL** on **MySQL Workbench**.

![Database visualization](https://s3-us-west-2.amazonaws.com/secure.notion-static.com/73c4cb4d-c89f-43e9-8a8d-6ea257460797/Untitled.png)
This is the visualization of the database system for the website.

## Website

- http://localhost/masttechdatabase/index.html

The website welcomes the users with an entry point where they can authorize themselves. Staff members can add and view the data, while the customers have two options:
1. To view products as a whole or,
2. To choose the best fitting product for their needs according to their special filter criteria.

## Website Overview
### Visit the Website
Upon visiting, users are greeted with a streamlined authorization portal. Here's what each user can expect:

Staff Members: They have the privileges to both add new product data and visualize the existing database.

Customers: They are presented with two primary functionalities:

A transparent view of all products.
A tailored experience, letting them discern the best products aligned with their unique needs.


## **User Roles and Workflow**

The website has been crafted with a robust user hierarchy, all under the meticulous oversight of the **Admin**. This ensures a controlled and structured workflow process.

### **User Roles**:

- **Admin**: The central authority on the platform. Their responsibilities span across various facets, most prominently the registration of staff members.
- **Staff1 (Adder)**: Primarily responsible for adding new product details to the database.
- **Staff2 (Checker)**: Ensures the accuracy and consistency of data by reviewing and verifying the information added by the Adder.
- **Staff3 (Approver)**: Upon the Checker's validation, they evaluate the product details and, if satisfactory, greenlight its release.
- **Customer**: The end users, they gain access to view products once they have successfully navigated the Adder > Checker > Approver workflow.

### **Product Release Workflow**:

For a product to be visible to the customers, it must pass through a rigorous three-tier validation process:

1. **Addition** by the Adder.
2. **Verification** by the Checker for data consistency and accuracy.
3. **Approval** by the Approver for final release.

Only products that successfully traverse all these stages are published and made available to customers.

