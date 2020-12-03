<?php


class Product
{
    private $id,$name,$description,$price_iva,$price_no_iva,$category_id,$units,$activated,$created_at,$last_modified,$iva;

    public function __construct($activated,$name, $description, $units,$type_price,$price,$iva, $category_id)
    {
        $this->activated = $activated;
        $this->name = $name;
        $this->description = $description;
        $this->units = $units;
        $this->category_id = $category_id;
        $this->iva = $iva;
        $this->created_at = new DateTime();
        $this->last_modified = new DateTime();
        if($type_price==1){
            $this->price_iva = $price;
            $this->price_no_iva = $this->getPriceIva() - (($this->getPriceIva()*$iva)/100);
        } else{
            $this->price_no_iva = $price;
            $this->price_iva = $this->getPriceNoIva() + (($this->getPriceNoIva()*$iva)/100);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPriceIva()
    {
        return $this->price_iva;
    }

    public function setPriceIva($price_iva)
    {
        $this->price_iva = $price_iva;
    }

    public function getPriceNoIva()
    {
        return $this->price_no_iva;
    }

    public function setPriceNoIva($price_no_iva)
    {
        $this->price_no_iva = $price_no_iva;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function setUnits($units)
    {
        $this->units = $units;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getActivated()
    {
        return $this->activated;
    }

    public function setActivated($activated)
    {
        $this->activated = $activated;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getLastModified()
    {
        return $this->last_modified;
    }

    public function setLastModified($last_modified)
    {
        $this->last_modified = $last_modified;
    }

    public function getDatabaseValues(){
        return array($this->activated,$this->name,$this->description,$this->units,$this->category_id,$this->iva,$this->price_iva,$this->price_no_iva,$this->created_at->format("Y-m-d"), $this->last_modified->format("Y-m-d"));
    }



}