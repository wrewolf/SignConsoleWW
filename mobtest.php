<?php

  /*
  __PocketMine Plugin__
  name=mobTest
  description=Create some NPCs!
  version=1.1
  author=zhuowei
  class=MobTest
  apiversion=10,11,12
  */

  /*
  Small Changelog
  ===============

  1.0: Initial release

  1.1: NPCs now chase you

  */


  class MobTest implements Plugin
  {

    private $api, $npclist, $config, $path, $ticksuntilupdate;

    public function __construct(ServerAPI $api, $server = false)
    {
      $this->api              = $api;
      $this->npclist          = array();
      $this->ticksuntilupdate = 0;
    }

    public function init()
    {
      $this->path = $this->api->plugin->configPath($this);
      $this->api->console->register("spawnmob", "Add an Mob. /spawnmob [name] [player location] OR /spawnmob [name] <x> <y> <z> <world>", array($this, "command"));
      $this->api->console->register("rmmob", "Remove an NPC. /rmmob]", array($this, "rmcommand"));
      $this->api->console->register("lsmob", "List an NPC. /lsmob]", array($this, "lscommand"));
      $this->api->schedule($this->ticksuntilupdate, array($this, "tickHandler"), array(), true, "server.schedule");
      $this->config = new Config($this->path . "config.yml", CONFIG_YAML, array(
        "mobs" => array(),
      ));
      $this->spawnAllNpcs();

    }

    public function spawnAllNpcs()
    {
      $npcconflist = $this->config->get("mobs");
      if (!is_array($npcconflist)) {
        $this->config->set("npcs", array());
        return;
      }
      foreach (array_keys($npcconflist) as $pname) {
        $p   = $npcconflist[$pname];
        $pos = new Position($p["Pos"]
