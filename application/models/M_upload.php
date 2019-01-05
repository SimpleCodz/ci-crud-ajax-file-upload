<?php
class M_upload extends CI_Model{
	
	function simpan_upload($judul,$image){
		$data = array(
			'judul' => $judul,
			'gambar' => $image
		);
	    $result= $this->db->insert('tb_galeri',$data);
	    return $result;
	}
	
	function getData(){
		return $this->db->get('tb_galeri')->result();
	}

	function getById($id){
		$query = $this->db->get_where('tb_galeri', array('id' => $id));
		return $query->row();
	}

	function update($id, $judul, $gambar){
		if($gambar == null){
			$data = array('judul' => $judul);	
		}else{
			$data = array(
				'judul' => $judul,
				'gambar' => $gambar
			);
		}
		$this->db->where('id', $id);
		$result = $this->db->update('tb_galeri', $data);
		return $result;
	}

	function hapus($id){
		$this->db->where('id', $id);
		return $this->db->delete('tb_galeri');
	}
}