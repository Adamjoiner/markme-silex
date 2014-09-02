<?php

/**
 * @author M.Paraiso
 */

namespace MarkMe\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DomCrawler\Crawler;
use \Symfony\Component\Validator\Constraints;

class Bookmark {

    function index(Application $app, $_format) {
        /* @var $app \MarkMe\App  */
        $user = $app->security->getToken()->getUser();
        $limit = intval($app["request"]->query->get("limit", 50));
        $offset = intval($app['request']->query->get("offset", 0)) * $limit;
        $bookmarks = $app->bookmarks->findBy(array('user' => $user), array('createdAt' => 'DESC'), $limit, $offset);
        return $app->serializer->serialize(array(
                    'status' => 200,
                    'bookmarks' => $bookmarks,
                    'limit' => $limit,
                    'offset' => $offset
                        ), $_format);
    }

    function findByTags(Application $app, Request $req, $tags, $_format) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        $limit = $req->query->get("limit", 100);
        $offset = $req->query->get("offset", 0);
        $bookmarks = $app->bookmarks->findByTag($tags, $user, $limit, $offset * $limit);
        return $app->serializer->serialize(array('status' => 200, 'bookmarks' => $bookmarks), 'json');
    }

    /**
     * trouver par tag,description ou titre
     * @param \Silex\Application $app
     */
    function search(Request $request, Application $app, $_format) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        $limit = $request->query->get('limit', 100);
        $offset = $request->query->get('offset', 0);
        $bookmarks = $app->bookmarks->search($request->get('q'), $user, $limit, $offset * $limit);
        return $app->serializer->serialize(array('status' => 200, 'bookmarks' => $bookmarks), 'json');
    }

    function create(Application $app, Request $req, $_format) {
        /* @var \MarkMe\App $app */
        $bookmark = $app->serializer->deserialize($req->getContent(), '\MarkMe\Entity\Bookmark', 'json');
        /* @var \MarkMe\Entity\Bookmark $bookmark */
        $bookmark->setUser($app->security->getToken()->getUser());
        $bookmark->setPrivate(true);
        $bookmark->setCreatedAt(new \DateTime());
        $errors = $app->validator->validate($bookmark);
        if (count($errors) > 0) {
            throw new \Symfony\Component\Validator\Exception\ValidatorException($errors);
        }
        $app->bookmarks->create($bookmark);
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), 'json');
    }

    function read(Application $app, $id, $_format) {
        $user = $app->security->getToken()->getUser();
        $bookmark = $app->bookmarks->findOneBy(array('id' => $id, 'user' => $user)) OR $app->abort(404, "bookmark with id $id not found");
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), $_format);
    }

    function update(Application $app, Request $req, $_format) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        /* @var \MarkMe\Entity\Bookmark $bookmark */
        $bookmark = $app->bookmarks->findOneBy(array('id' => $app->request->get('id'), 'user' => $user));
        if ($bookmark == NULL) {
            return new Response($app->serializer->serialize(array('status' => 404, 'message' => 'not found'), 'json'), 404);
        }
        $candidate = $app->serializer->deserialize($req->getContent(), '\MarkMe\Entity\Bookmark', 'json');
        $bookmark->setTitle($candidate->getTitle());
        $bookmark->setDescription($candidate->getDescription());
        $bookmark->setUrl($candidate->getUrl());
        $bookmark->setTags($candidate->getTags());
        $errors = $app->validator->validate($bookmark);
        if (count($errors) > 0) {
            throw new \Symfony\Component\Validator\Exception\ValidatorException($errors);
        }
        $app->bookmarks->update($bookmark);
        return $app->serializer->serialize(array('status' => 200, 'bookmark' => $bookmark), 'json');
    }

    function delete(Application $app, $id, $_format) {
        /* @var \MarkMe\App $app */
        $user = $app->security->getToken()->getUser();
        $bookmark = $app->bookmarks->findOneBy(array('id' => $id, 'user' => $user));
        if (NULL == $bookmark) {
            return new Response($app->serializer->serialize(array('status' => 404, 'message' => 'bookmark not found'), 'json'), 404);
        }
        $app->bookmarks->delete($bookmark);
        return $app->serializer->serialize(array('status' => 200), 'json');
    }

    function export(Request $req, Application $app, $_format) {
        $user = $app->security->getToken()->getUser();
        $string = $app->bookmarks->export($user);
        return $app->serializer->serialize(array('export' => $string), $_format);
    }

    /**
     * EN : import bookmarks
     */
    function import(Request $req, Application $app, $_format) {
        $user = $app->security->getToken()->getUser();
        $bookmarkImports = $app->serializer->deserialize($req->getContent(), '\MarkMe\Entity\BookmarkImportCollection', $_format);
        $app->bookmarks->import($bookmarkImports, $user);
        return $app->serializer->serialize(array('status' => 200, 'message' => 'ok'), $_format);
    }

    function suggestBookmarkData(Application $app, Request $req, $_format) {
        $url = $req->query->get('url');
        //if cached return cache value
        $constraint = new Constraints\Url();
        $errors = $app->validator->validateValue($url, $constraint);
        if ($url && count($errors) == 0) {
            $result = $app->bookmarks->suggest($url);
            return $app->serializer->serialize($result, $_format);
        }
        return new Response("$url is not a valid Url", 500);
    }

}
