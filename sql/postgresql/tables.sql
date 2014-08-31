/*==============================================================*/
/* DBMS name:      PostgreSQL 8 (WFW)                           */
/* Created on:     31/08/2014 21:37:39                          */
/*==============================================================*/


drop index  if exists MAIL_CONTACT_PK;

drop table if exists MAIL_CONTACT  CASCADE;

drop table if exists MAIL_MESSAGE  CASCADE;

drop index  if exists MAIL_SERVER_PK;

drop table if exists MAIL_SERVER  CASCADE;

/*==============================================================*/
/* Table: MAIL_CONTACT                                          */
/*==============================================================*/
create table MAIL_CONTACT (
   MAIL_CONTACT_ID      SERIAL               not null,
   MAIL_ADDRESS         VARCHAR(260)         not null,
   FIRSTNAME            VARCHAR(80)          not null,
   LASTNAME             VARCHAR(80)          not null,
   constraint PK_MAIL_CONTACT primary key (MAIL_CONTACT_ID)
);

/*==============================================================*/
/* Index: MAIL_CONTACT_PK                                       */
/*==============================================================*/
create unique index MAIL_CONTACT_PK on MAIL_CONTACT (
MAIL_CONTACT_ID
);

/*==============================================================*/
/* Table: MAIL_MESSAGE                                          */
/*==============================================================*/
create table MAIL_MESSAGE (
   "FROM"               VARCHAR(260)         not null,
   "TO"                 VARCHAR(260)         not null,
   MSG                  TEXT                 not null,
   SUBJECT              VARCHAR(128)         not null,
   FROM_NAME            VARCHAR(80)          null,
   NOTIFY               VARCHAR(260)         null,
   CONTENT_TYPE         VARCHAR(80)          not null
);

/*==============================================================*/
/* Table: MAIL_SERVER                                           */
/*==============================================================*/
create table MAIL_SERVER (
   MAIL_SERVER_ID       SERIAL               not null,
   SERVER_ADR           VARCHAR(128)         not null,
   PORT_NUM             INT4                 not null,
   constraint PK_MAIL_SERVER primary key (MAIL_SERVER_ID)
);

/*==============================================================*/
/* Index: MAIL_SERVER_PK                                        */
/*==============================================================*/
create unique index MAIL_SERVER_PK on MAIL_SERVER (
MAIL_SERVER_ID
);

