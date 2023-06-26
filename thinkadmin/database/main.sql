/*
 Navicat Premium Data Transfer

 Source Server         : music
 Source Server Type    : SQLite
 Source Server Version : 3035005
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005
 File Encoding         : 65001

 Date: 26/06/2023 06:44:34
*/

PRAGMA foreign_keys = false;

-- ----------------------------
-- Table structure for musics
-- ----------------------------
DROP TABLE IF EXISTS "musics";
CREATE TABLE "musics" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "playlist_id" INTEGER(11) NOT NULL DEFAULT 0,
  "name" VARCHAR(255) NOT NULL DEFAULT '',
  "artist" VARCHAR(64) DEFAULT '',
  "url" VARCHAR(256) DEFAULT '',
  "path" VARCHAR(256) DEFAULT '',
  "pic" VARCHAR(256) DEFAULT '',
  "lrc" VARCHAR(256) DEFAULT '',
  "srt" VARCHAR(256) DEFAULT '',
  "create_time" DATETIME,
  "update_time" DATETIME,
  "cost_time" VARCHAR(32),
  "cover" VARCHAR(256) DEFAULT '',
  "mp4" VARCHAR(256) DEFAULT '',
  "type" VARCHAR(32) DEFAULT ''
);

-- ----------------------------
-- Records of musics
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for playlist
-- ----------------------------
DROP TABLE IF EXISTS "playlist";
CREATE TABLE "playlist" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "type" VARCHAR(16),
  "title" VARCHAR(64),
  "url" VARCHAR(256),
  "create_time" DATETIME,
  "update_time" DATETIME,
  "last_id" INTEGER(11)
);

-- ----------------------------
-- Records of playlist
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Auto increment value for musics
-- ----------------------------

PRAGMA foreign_keys = true;
