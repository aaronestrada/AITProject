<?php
namespace models;
use \framework\BaseModel;
use framework\BaseQuery;

class Document extends BaseModel {
    protected $primaryKey = ['id'];

    protected $fields = [
        'id' => 'integer',
        'author_id' => 'integer',
        'status' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'price' => 'float',
        'created_at' => 'date',
        'modified_at' => 'date',
        'published_at' => 'date',
        'filename' => 'string'
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Return list of tags for a Document object
     * @return array|null List of Tag objects or null if not found
     */
    public function getTags() {
        if($this->id !== null) {
            $tagQuery = new BaseQuery('tg');
            $tagQuery->select()
                ->join('document_tag', 'dt', ['tg.id = dt.tag_id'])
                ->join('document', 'doc', ['dt.document_id = doc.id'])
                ->andWhere(['doc.id' => $this->id]);

            $objTags = new Tag();
            $tagList = $objTags->queryAllFromObject($tagQuery);

            if($tagList !== null)
                return $tagList;
        }
        return [];
    }

    /**
     * Return author object for a document
     * @return BaseModel|null
     */
    public function getAuthor() {
        if($this->id !== null) {
            $objAuthor = new Author();
            return $objAuthor->fetchOne($this->author_id);
        }
        return null;
    }
}