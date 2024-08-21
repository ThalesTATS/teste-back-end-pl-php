<?php

namespace App\Services;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Anexo;

class AnexoService
{

    public static function upload($file, $observacao, $entityManager, $uploads_directory) {        
        try {
            $anexo = new Anexo();
            $anexo->setNome($file->getClientOriginalName());
            $anexo->setExtensao(self::getFileExtension($anexo->getNome()));
            $anexo->setTamanho($file->getSize());
            $observacao->addAnexo($anexo);
            $entityManager->persist($anexo);
            $entityManager->flush();

            $anexo = $observacao->getAnexos()->last();
            $anexo->setUrl('/'.$anexo->getId().'.'.$anexo->getExtensao());
            $file->move($uploads_directory.'/', $anexo->getId().'.'.$anexo->getExtensao());
            $entityManager->flush();
            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    public static function remove(Anexo $anexo, $entityManager, $uploads_directory) {
        $filesystem = new Filesystem();
        $filesystem->remove([$uploads_directory.$anexo->getUrl(), $anexo->getNome()]);
        $entityManager->remove($anexo);
        $entityManager->flush();
    }

    public static function getFileExtension($name){
        $ext = explode('.', $name);
        return end($ext);
    }
}
