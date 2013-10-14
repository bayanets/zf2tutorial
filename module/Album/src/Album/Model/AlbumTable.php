<?php
namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class AlbumTable
{
	protected $tableGateway; //используется для выполнения операций над таблицей БД альбома.

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()  //возвращает из БД все альбомы построчно
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	public function getAlbum($id) //возвращает одну запись в виде объекта Album
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}

	public function saveAlbum(Album $album) //или создает новую запись в БД или обновляет уже существующую запись
	{
		$data = array(
				'artist' => $album->artist,
				'title'  => $album->title,
		);

		$id = (int)$album->id;
		if ($id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if ($this->getAlbum($id)) {
				$this->tableGateway->update($data, array('id' => $id));
			} else {
				throw new \Exception('Form id does not exist');
			}
		}
	}

	public function deleteAlbum($id) //удаляет запись полностью
	{
		$this->tableGateway->delete(array('id' => $id));
	}
}