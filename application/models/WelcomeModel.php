<?php

class WelcomeModel extends CI_Model
{
    private function queryTable($area, $from, $to) {
        $this->db->from('report_product');
        $this->db->join('store', 'store.store_id = report_product.store_id');
        $this->db->join('product', 'product.product_id = report_product.product_id');
        $this->db->join('product_brand', 'product_brand.brand_id = product.brand_id');
        $this->db->join('store_area', 'store_area.area_id = store.area_id');
        $this->db->where_in('store.area_id', $area);
        $this->db->where('report_product.tanggal >= ', $from);
        $this->db->where('report_product.tanggal <= ', $to);
    }

    public function chart($area, $from, $to)
    {
        $this->db->select(['store_area.area_name', 'SUM(compliance) AS compliance_total', 'COUNT(report_id) AS row_total']);
        $this->queryTable($area, $from, $to);
        $this->db->group_by('store_area.area_name,  store.area_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function datatables($area, $from, $to)
    {
        $this->db->select(['store_area.area_name', 'product_brand.brand_name', 'SUM(compliance) AS compliance_total', 'COUNT(report_id) AS row_total']);
        $this->queryTable($area, $from, $to);
        $this->db->group_by('store_area.area_name, product_brand.brand_name, store.area_id');
        $query = $this->db->get();
        return $query->result();
    }
}