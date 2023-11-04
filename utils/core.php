<?php

include_once 'db.php';

class HxxError {
  const DB_EXISTS = 1;
  public function __construct(public string $message, public int $code) {}
}

class HxxSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
  public function close(): bool {
    return TRUE;
  }

  public function destroy(string $id): bool {
    $db = db_connection();
    $stmt = $db->prepare('DELETE FROM sessions WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
    ]);
    return TRUE;
  }

  public function gc(int $max_lifetime): int|false {
    $db = db_connection();
    $stmt = $db->prepare('DELETE FROM sessions WHERE last_access < :last_access');
    $stmt->execute([
      ':last_access' => time() - $max_lifetime,
    ]);
    return TRUE;
  }

  public function open(string $path, string $name): bool {
    return TRUE;
  }

  public function read(string $id): string|false {
    $db = db_connection();
    $stmt = $db->prepare('SELECT data FROM sessions WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
    ]);
    $row = $stmt->fetch(PDO::FETCH_COLUMN);
    if ($row) {
      return $row;
    }
    return FALSE;
  }

  public function write(string $id, string $data): bool {
    $db = db_connection();
    $stmt = $db->prepare('INSERT INTO sessions (id, data, last_access) VALUES (:id, :data, :last_access) ON CONFLICT (id) DO UPDATE SET data = :data, last_access = :last_access');
    $stmt->execute([
      ':id' => $id,
      ':data' => $data,
      ':last_access' => time(),
    ]);
    return TRUE;
  }

  public function create_sid(): string {
    return bin2hex(random_bytes(16));
  }

  public function validateId(string $id): bool {
    $db = db_connection();
    $stmt = $db->prepare('SELECT id FROM sessions WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
    ]);
    $row = $stmt->fetch(PDO::FETCH_COLUMN);
    if ($row) {
      return TRUE;
    }
    return FALSE;
  }

  public function updateTimestamp(string $id, string $data): bool {
    $db = db_connection();
    $stmt = $db->prepare('UPDATE sessions SET last_access = :last_access WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
      ':last_access' => time(),
    ]);
    return TRUE;
  }
}
