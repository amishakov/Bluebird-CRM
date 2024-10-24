<?php

interface CRM_NYSS_BAO_Integration_WebsiteEventInterface {

  /**
   * Main entry point for processing an event
   *
   * @return $this
   * Processes Website Event.
   */
  public function process(int $contact_id): static;

  function getParentTagName(): string;

  //function findParentTagId(): int;
  public function getEventDetails(): string;

  public function getEventDescription(): string;

  public function getActivityData(): ?string;

  /**
   * @return bool Specifies whether there is an event type specific archive
   * table associated with the event type.
   */
  public function hasArchiveTable(): bool;

  /**
   * Specifies the event type specific database table name. Used by
   * getArchiveSQL()
   *
   * @return string
   */
  public function getArchiveTableName(): string;

  /**
   * Specifies event type specific database table fields for INSERT statement
   * generated by getArchiveSQL()
   *
   * @return array|null
   */
  public function getArchiveFields(): ?array;

  /**
   * Returns values associated with getArchiveFields()
   *
   * @return array|null
   */
  public function getArchiveValues(): ?array;

  /**
   * Generates an SQL statement that can be used to archive event type specific
   * values to an event type specific table.
   *
   * @param int $archive_id
   * @param string|null $prefix
   *
   * @return string
   */
  public function getArchiveSQL(int $archive_id, ?string $prefix = NULL): string;

}