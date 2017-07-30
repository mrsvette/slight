<?php
namespace Model;

require_once __DIR__ . '/base.php';

class PostModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'post';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['status, post_type, author_id', 'required'],
            ['created_at', 'required', 'on'=>'create'],
            ['author_id', 'numerical', 'integerOnly' => true],
        ];
    }

    public function getListStatus()
    {
        return [ 'draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived' ];
    }

    public static function string2array($tags)
    {
        return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags)
    {
        return implode(', ',$tags);
    }

    public static function createSlug($str)
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        $str = trim($str, '-');
        return $str;
    }
    
    public function getPosts($data)
    {
        $sql = "SELECT t.status, c.post_id, c.title, c.content, c.slug, l.id, l.language_name    
        FROM tbl_post t 
        LEFT JOIN tbl_post_content c ON c.post_id = t.id 
        LEFT JOIN tbl_post_language l ON l.id = c.language  
        WHERE 1";

        if (isset($data['just_default'])) {
            $sql .= ' AND l.is_default = 1';
        }

        $sql .= ' ORDER BY t.id DESC';
        $rows = R::getAll( $sql );

        return $rows;
    }
}
