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
    
    public function getPostDetail($id)
    {
        $sql = "SELECT c.post_id, t.status, t.allow_comment, t.tags, t.created_at, t.updated_at, 
          c.title, c.content, c.slug, l.id AS language_id, 
          c.meta_keywords, c.meta_description, l.language_name, ad.username AS author_name 
        FROM tbl_post t 
        LEFT JOIN tbl_post_content c ON c.post_id = t.id 
        LEFT JOIN tbl_post_language l ON l.id = c.language  
        LEFT JOIN tbl_admin ad ON ad.id = t.author_id  
        WHERE t.id =:id";

        $rows = R::getAll( $sql, ['id'=>$id] );

        $items = [
            'id' => $rows[0]['post_id'],
            'status' => $rows[0]['status'],
            'allow_comment' => $rows[0]['allow_comment'],
            'tags' => (!empty($rows[0]['tags']))? self::string2array($rows[0]['tags']) : array(),
            'tags_string' => $rows[0]['tags'],
            'author' => $rows[0]['author_name'],
            'created_at' => $rows[0]['created_at'],
            'updated_at' => $rows[0]['updated_at'],
        ];
        foreach ($rows as $i => $row) {
            $items['content'][$row['language_id']] = [
                'title' => $row['title'],
                'slug' => $row['slug'],
                'content' => $row['content'],
                'meta_keywords' => $row['meta_keywords'],
                'meta_description' => $row['meta_description']
            ];
        }

        $sql2 = "SELECT t.category_id    
        FROM tbl_post_in_category t 
        WHERE t.post_id =:post_id";

        $rows2 = R::getAll( $sql2, ['post_id'=>$id] );
        $category = [];
        foreach ($rows2 as $j => $row2) {
            array_push($category, $row2['category_id']);
        }
        $items['category'] = $category;

        return $items;
    }
}